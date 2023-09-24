<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Domain\Document\ComplianceFile;
use App\Tests\Feature\BaseTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ComplianceFilesControllerTest extends BaseTestCase
{
    public function testUserCanUploadComplianceFile(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s/files', $testCompany->getId()));

        self::$client->submitForm('Submit', [
            'compliance_file' => [
                'name' => 'My Compliance File',
                'document' => ['file' => new UploadedFile('tests/fixtures/upload/pixel.png', 'pixel.png')],
            ],
        ]);

        self::assertResponseIsSuccessful();
        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        self::assertCount(1, $testCompany->getComplianceFiles());
        $file = $testCompany->getComplianceFiles()->first();
        self::assertInstanceOf(ComplianceFile::class, $file);
        self::assertStringContainsString('pixel.png', (string) $file->getKey());
        self::assertSame('My Compliance File', $file->getName());
        self::assertSame($this->getTestUser()->getEmail(), $file->getUploader());

        self::$client->followRedirects(false);
        self::$client->request('GET', sprintf('/merchants/%s/files/%s', $testCompany->getId(), $file->getId()));
        self::assertResponseRedirects();
        $location = (string) self::$client->getResponse()->headers->get('Location');
        self::assertMatchesRegularExpression('/X-Amz-Expires=(60|59)/', $location);
    }

    public function testUserCanNotUploadComplianceFileWithWrongMimeType(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s/files', $testCompany->getId()));

        self::$client->submitForm('Submit', [
            'compliance_file' => [
                'name' => 'My Compliance File',
                'document' => ['file' => new UploadedFile('tests/fixtures/upload/test-file.txt', 'test-file.txt')],
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('.invalid-feedback', 'The mime type of the file is invalid');
    }

    /**
     * @group smoke
     */
    public function testItShowsFilesPage(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/files', $this->getTestCompany()->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('company-documents-list');
    }

    /**
     * @group smoke
     */
    public function testItListsFiles(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/files/list', $this->getTestCompany()->getId()));

        $this->assertGridResponse();
    }
}
