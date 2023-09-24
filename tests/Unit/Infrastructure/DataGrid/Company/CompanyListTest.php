<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Company;

use App\Domain\Document\Company\Company;
use App\Domain\Transformer\CompanyTransformer;
use App\Infrastructure\DataGrid\Company\CompanyList;
use App\Infrastructure\Repository\Company\CompanyRepository;
use App\Tests\Unit\UnitTestCase;

class CompanyListTest extends UnitTestCase
{
    public function testItUsesTransformerToTransformCompanyIntoArray(): void
    {
        $company = $this->createStub(Company::class);

        $transformer = $this->createMock(CompanyTransformer::class);
        $transformer->expects(self::once())->method('transform')->with($company);

        $companyList = new CompanyList($this->createStub(CompanyRepository::class), $transformer);
        $companyList->transform($company, 0);
    }
}
