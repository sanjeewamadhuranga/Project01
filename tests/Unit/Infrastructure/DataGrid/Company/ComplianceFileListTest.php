<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Company;

use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\Pagination;
use App\Domain\Document\Company\Company;
use App\Domain\Document\ComplianceFile;
use App\Infrastructure\DataGrid\Company\ComplianceFileList;
use App\Tests\Unit\UnitTestCase;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class ComplianceFileListTest extends UnitTestCase
{
    public function testItTransformsComplianceFileIntoArray(): void
    {
        $id = '61f0213b6c0d85172231b50b';
        $name = 'compliance file name';
        $key = 'compliance file key';
        $uploader = 'some uploader';
        $createdAt = new DateTime();

        $complianceFile = $this->createStub(ComplianceFile::class);
        $complianceFile->method('getId')->willReturn($id);
        $complianceFile->method('getName')->willReturn($name);
        $complianceFile->method('getKey')->willReturn($key);
        $complianceFile->method('getUploader')->willReturn($uploader);
        $complianceFile->method('getCreatedAt')->willReturn($createdAt);

        $complianceFileList = new ComplianceFileList($this->createStub(Company::class));

        self::assertSame([
            'id' => $id,
            'name' => $name,
            'key' => $key,
            'uploader' => $uploader,
            'createdAt' => $createdAt,
        ], $complianceFileList->transform($complianceFile, 0));
    }

    public function testItReturnsComplianceFilesFromCompany(): void
    {
        $complianceFiles = new ArrayCollection([$this->createStub(ComplianceFile::class)]);
        $company = $this->createMock(Company::class);
        $company->expects(self::once())->method('getComplianceFiles')->willReturn($complianceFiles);

        $complianceFileList = new ComplianceFileList($company);

        $gridRequest = $this->createStub(GridRequest::class);
        $gridRequest->method('getPagination')->willReturn(new Pagination());
        $result = $complianceFileList->getData($gridRequest);

        self::assertSame($complianceFiles, $result->getItems());
    }
}
