<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Application\Security\CognitoUser;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Domain\Document\Role\Role;
use App\Domain\Settings\FeatureInterface;
use App\Domain\Settings\Features;
use App\Infrastructure\Security\CognitoUserManager;
use App\Tests\Feature\BaseTestCase;
use Happyr\ServiceMocking\ServiceMock;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

class OnboardingControllerTest extends BaseTestCase
{
    private Features $features;

    private CognitoUserManager&MockObject $cognitoUserManager;

    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTouchesDb();
        $this->features = self::getContainer()->get(Features::class);
        $this->cognitoUserManager = $this->createMock(CognitoUserManager::class);
        $this->company = $this->getTestCompany();

        ServiceMock::swap(self::$client->getContainer()->get(CognitoUserManager::class), $this->cognitoUserManager);
    }

    public function testAddingUserToTheCompany(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/onboarding/add', $this->company->getId()));
        self::assertResponseIsSuccessful();

        $user = $this->createStub(CognitoUser::class);
        $user->method('getSub')->willReturn('test-sub-123');

        $this->cognitoUserManager->expects(self::once())
            ->method('getUserByIdentifier')
            ->with(CognitoUser::ATTRIBUTE_EMAIL, 'abc@dummy.email')
            ->willReturn($user);

        self::$client->submitForm('Next', [
            'user_search' => [
                'identifier' => 'abc@dummy.email',
            ],
        ]);

        $this->cognitoUserManager->expects(self::once())->method('getUserBySub')->with('test-sub-123')->willReturn($user);

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testAddingUserToTheCompanyWhenNoCognitoUserFoundWithEmailAsIdentifier(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/onboarding/add', $this->company->getId()));
        self::assertResponseIsSuccessful();

        $this->cognitoUserManager->expects(self::once())
            ->method('getUserByIdentifier')
            ->with(CognitoUser::ATTRIBUTE_EMAIL, 'xxx@example.com')
            ->willReturn(null);

        self::$client->submitForm('Next', [
            'user_search' => [
                'identifier' => 'xxx@example.com',
            ],
        ]);
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testAddingUserToTheCompanyWhenNoCognitoUserFoundWithSubAsIdentifier(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/onboarding/add', $this->company->getId()));
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Next', [
            'user_search' => [
                'identifier' => 'c87b0f98-68a3-4050-aeec-1643632d9de0',
            ],
        ]);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.alert.alert-danger', 'User not found!');
    }

    public function testFailingRenderingAssignFormWhenUserIsNotFound(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/onboarding/assign/test-123', $this->company->getId()));
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testAssigningUserFromCognitoUserToCompanyWhenKycIsDisabled(): void
    {
        $this->assigningUserFromCognitoUserToCompanyWhenKycIsEnabled(false);
    }

    public function testAssigningExistingUserToCompany(): void
    {
        $this->assigningUserFromCognitoUserToCompanyWhenKycIsEnabled(false);
        self::$client->request('GET', sprintf('/merchants/%s/users/onboarding/assign/test-sub-123', $this->company->getId()));
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Next', [
            'user' => [
                'firstName' => 'testFirstName',
                'lastName' => 'testLastName',
                'mobile' => '+99123445566',
                'roles' => [
                    $this->getTestRole()->getId(),
                ],
            ],
        ]);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.alert.alert-danger', 'This user already belongs to this company!');
    }

    public function testAssigningUserFromCognitoUserToCompanyWhenKycIsEnabled(): void
    {
        $this->assigningUserFromCognitoUserToCompanyWhenKycIsEnabled(kycData: [
            'addresses' => [
                'buildingName' => 'Building A',
                'buildingNumber' => '15',
                'flatNumber' => '15',
                'street' => 'Street Name',
                'town' => 'Great City',
                'country' => 'AFG',
                'postCode' => '12345-1234',
            ],
        ]);
    }

    public function testCreatingUserAndAddingToTheCompany(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/onboarding/create/testIdentifier@domain.com', $this->company->getId()));
        self::assertResponseIsSuccessful();

        $this->cognitoUserManager->expects(self::once())->method('createIdentityFromUser')
            ->with(self::isInstanceOf(User::class), self::isInstanceOf(Company::class))
            ->willReturnCallback(function (User $user) {
                $cognitoUser = $this->createStub(CognitoUser::class);
                $cognitoUser->method('getSub')->willReturn('test-123');
                $user->setSub($cognitoUser->getSub());
                $user->setId($cognitoUser->getSub());

                return $cognitoUser;
            });

        self::$client->submitForm('Next', [
            'user' => [
                'firstName' => 'testFirstName',
                'lastName' => 'testLastName',
                'mobile' => '+99123445566',
                'roles' => [
                    $this->getTestRole()->getId(),
                ],
            ],
        ]);
        self::$client->followRedirect();

        $this->assertCompanyHasUserWithSub('test-123');
        self::assertResponseIsSuccessful();
    }

    private function assertCompanyHasUserWithSub(string $sub): void
    {
        self::assertInstanceOf(User::class, $this->refresh($this->company)->getUser($sub));
    }

    /**
     * @param array<string, mixed> $kycData
     */
    private function assigningUserFromCognitoUserToCompanyWhenKycIsEnabled(bool $enabled = true, array $kycData = []): void
    {
        $user = $this->createStub(CognitoUser::class);
        $user->method('getSub')->willReturn('test-sub-123');
        $this->cognitoUserManager->expects(self::atLeastOnce())->method('getUserBySub')->with('test-sub-123')->willReturn($user);

        $this->features->enableFeature(FeatureInterface::SYSTEM_KYC, $enabled);
        self::$client->request('GET', sprintf('/merchants/%s/users/onboarding/assign/test-sub-123', $this->company->getId()));
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Next', [
           'user' => array_merge_recursive([
               'firstName' => 'testFirstName',
               'lastName' => 'testLastName',
               'mobile' => '+99123445566',
               'roles' => [
                   $this->getTestRole()->getId(),
               ],
           ], $kycData),
        ]);
        self::$client->followRedirect();

        self::assertResponseIsSuccessful();
        $this->assertCompanyHasUserWithSub('test-sub-123');
    }

    private function getTestRole(): Role
    {
        return static::$fixtures['test_role_1']; // @phpstan-ignore-line
    }
}
