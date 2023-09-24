<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Compliance;

use App\Application\Compliance\Onfido;
use App\Application\Compliance\ResourceType;
use App\Domain\Company\ComplianceStatus;
use App\Domain\Company\OnfidoResourcesResponse;
use App\Domain\Document\Company\Address;
use App\Domain\Document\Company\ComplianceReport;
use App\Domain\Document\Company\User;
use App\Tests\Unit\UnitTestCase;
use DateTime;
use InvalidArgumentException;
use Onfido\Api\DefaultApi;
use Onfido\Model\Address as OnfidoAddress;
use Onfido\Model\ApplicantRequest;
use Onfido\Model\ApplicantResponse;
use Onfido\Model\CheckResponse;
use Onfido\Model\ChecksList;
use Onfido\Model\DocumentResponse;
use Onfido\Model\DocumentsList;
use Onfido\Model\Error;
use Onfido\Model\LivePhotoResponse;
use Onfido\Model\LivePhotosList;
use Onfido\Model\LiveVideo;
use Onfido\Model\LiveVideosList;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use SplFileObject;

class OnfidoTest extends UnitTestCase
{
    private DefaultApi&MockObject $api;

    private Onfido $onfido;

    protected function setUp(): void
    {
        parent::setUp();

        $this->api = $this->createMock(DefaultApi::class);
        $this->onfido = new Onfido($this->api);
    }

    public function testItSubmitsUserWithAddressToOnfido(): void
    {
        $this->api->expects(self::once())
            ->method('createApplicant')
            ->with(self::equalTo($this->getTestApplicant()))
            ->willReturn(new ApplicantResponse(['id' => 'test-applicant-id']));

        self::assertSame('test-applicant-id', $this->onfido->createApplicant($this->getTestUser()));
    }

    public function testItWillReturnNullWhenThereIsNoApplicationId(): void
    {
        self::assertNull($this->onfido->getKycResources($this->getTestUser()));
    }

    public function testItWillReturnKycResourcesWhenApplicationIdIsValid(): void
    {
        $document = $this->createStub(DocumentResponse::class);
        $documentList = $this->createStub(DocumentsList::class);
        $documentList->method('getDocuments')->willReturn([$document]);
        $this->api->expects(self::once())->method('listDocuments')->with('test-applicant-id')->willReturn($documentList);

        $livePhoto = $this->createStub(LivePhotoResponse::class);
        $livePhotoList = $this->createStub(LivePhotosList::class);
        $livePhotoList->method('getLivePhotos')->willReturn([$livePhoto]);
        $this->api->expects(self::once())->method('listLivePhotos')->with('test-applicant-id')->willReturn($livePhotoList);

        $liveVideo = $this->createStub(LiveVideo::class);
        $liveVideoList = $this->createStub(LiveVideosList::class);
        $liveVideoList->method('getLiveVideos')->willReturn([$liveVideo]);
        $this->api->expects(self::once())->method('listLiveVideos')->with('test-applicant-id')->willReturn($liveVideoList);

        $check = $this->createStub(CheckResponse::class);
        $checkList = $this->createStub(ChecksList::class);
        $checkList->method('getChecks')->willReturn([$check]);
        $this->api->expects(self::once())->method('listChecks')->with('test-applicant-id')->willReturn($checkList);

        $response = $this->onfido->getKycResources($this->getTestUser(true, true));

        self::assertInstanceOf(OnfidoResourcesResponse::class, $response);
        self::assertSame([$document], $response->documents);
        self::assertSame([$livePhoto], $response->livePhotos);
        self::assertSame([$liveVideo], $response->liveVideos);
        self::assertSame([$check], $response->checks);
    }

    public function testItWillReturnEmptyArrayWhenApiReturnError(): void
    {
        $this->api->expects(self::once())->method('listDocuments')->with('test-applicant-id')->willReturn($this->createStub(Error::class));
        $this->api->expects(self::once())->method('listLivePhotos')->with('test-applicant-id')->willReturn($this->createStub(Error::class));
        $this->api->expects(self::once())->method('listLiveVideos')->with('test-applicant-id')->willReturn($this->createStub(Error::class));
        $this->api->expects(self::once())->method('listChecks')->with('test-applicant-id')->willReturn($this->createStub(Error::class));
        $response = $this->onfido->getKycResources($this->getTestUser(true, true));

        self::assertInstanceOf(OnfidoResourcesResponse::class, $response);
        self::assertSame([], $response->documents);
        self::assertSame([], $response->liveVideos);
        self::assertSame([], $response->livePhotos);
        self::assertSame([], $response->checks);
    }

    /**
     * @dataProvider resourceTypeProvider
     */
    public function testItWillDownloadFileWhenValidResourceTypeIsProvided(ResourceType $resourceType, string $methodName): void
    {
        $file = new SplFileObject('php://memory');
        $this->api->expects(self::once())->method($methodName)->with('testResourceId')->willReturn($file);
        $downloadResult = $this->onfido->getDownloadFile($resourceType, 'testResourceId');

        self::assertSame($file, $downloadResult);
    }

    public function testItWillThrowExceptionWhenUnsupportedDocumentProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->onfido->getDownloadFile(ResourceType::CHECK, 'test-resource-id');
    }

    /**
     * @dataProvider resourceTypeProvider
     */
    public function testItWillReturnNullWhenDowloadAbleAPIReturnNull(ResourceType $resourceType, string $methodName): void
    {
        $this->api->expects(self::once())->method($methodName)->with('test-resource-id')->willReturn($this->createStub(Error::class));

        $this->onfido->getDownloadFile($resourceType, 'test-resource-id');
    }

    /**
     * @return iterable<int, array{ResourceType,string}>
     */
    public function resourceTypeProvider(): iterable
    {
        yield [ResourceType::DOCUMENT, 'downloadDocument'];
        yield [ResourceType::LIVE_VIDEO, 'downloadLiveVideo'];
        yield [ResourceType::LIVE_PHOTO, 'downloadLivePhoto'];
    }

    public function testItSubmitsUserWithoutAddressToOnfido(): void
    {
        $this->api->expects(self::once())
            ->method('createApplicant')
            ->with(self::equalTo($this->getTestApplicant(false)))
            ->willReturn(new ApplicantResponse(['id' => 'test-applicant-no-address-id']));

        self::assertSame('test-applicant-no-address-id', $this->onfido->createApplicant($this->getTestUser(false)));
    }

    public function testItThrowsExceptionIfErrorIsReturned(): void
    {
        $this->api->expects(self::once())->method('createApplicant')->willReturn(new Error());
        $this->expectException(RuntimeException::class);

        $this->onfido->createApplicant($this->getTestUser(false));
    }

    private function getTestUser(bool $withAddress = true, bool $withComplianceReport = false): User
    {
        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setDob('2000-01-10');

        if ($withAddress) {
            $address = new Address();
            $address->setCountry('GBR');
            $address->setBuildingName('Test Building');
            $address->setStreet('Cecil Street');
            $address->setBuildingNumber('15A');
            $address->setFlatNumber('#14-11');
            $address->setPostCode('203040');
            $address->setTown('203040');
            $user->setAddresses($address);
        }

        if ($withComplianceReport) {
            $complianceReport = new ComplianceReport();
            $complianceReport->setUserId('test-userId');
            $complianceReport->setCompanyId('test-company-id');
            $complianceReport->setCheckId('test-check-id');
            $complianceReport->setState(ComplianceStatus::PENDING);
            $complianceReport->setApplicantId('test-applicant-id');
            $user->setComplianceReport($complianceReport);
        }

        return $user;
    }

    /**
     * @return ApplicantRequest<string, mixed>
     */
    private function getTestApplicant(bool $withAddress = true): ApplicantRequest
    {
        $applicant = new ApplicantRequest();
        $applicant->setFirstName('John');
        $applicant->setLastName('Doe');
        $applicant->setDob(new DateTime('2000-01-10'));

        if ($withAddress) {
            $address = new OnfidoAddress();
            $address->setCountry('GBR');
            $address->setBuildingName('Test Building');
            $address->setStreet('Cecil Street');
            $address->setBuildingNumber('15A');
            $address->setFlatNumber('#14-11');
            $address->setPostcode('203040');
            $address->setTown('203040');
            $applicant->setAddress($address);
        }

        return $applicant;
    }
}
