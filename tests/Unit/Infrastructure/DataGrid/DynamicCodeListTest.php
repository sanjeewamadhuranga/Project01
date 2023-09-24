<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Document\Company\Company;
use App\Domain\Document\DynamicCode;
use App\Domain\Document\Location\Location;
use App\Infrastructure\DataGrid\DynamicCodeList;
use App\Infrastructure\Repository\DynamicCodeRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;

class DynamicCodeListTest extends UnitTestCase
{
    public function testItTransformsDynamicCodeIntoArray(): void
    {
        $id = '61f021a163b571290822cddc';
        $code = 'code';
        $hits = 17;
        $companyName = 'company-name';
        $locationName = 'location-name';
        $targetUrl = 'target-url';
        $createdAt = new DateTime();
        $updatedAt = new DateTime();

        $company = $this->createStub(Company::class);
        $company->method('__toString')->willReturn($companyName);

        $location = $this->createStub(Location::class);
        $location->method('getName')->willReturn($locationName);

        $dynamicCode = $this->createStub(DynamicCode::class);
        $dynamicCode->method('getId')->willReturn($id);
        $dynamicCode->method('getCode')->willReturn($code);
        $dynamicCode->method('getHits')->willReturn($hits);
        $dynamicCode->method('getCompany')->willReturn($company);
        $dynamicCode->method('getLocation')->willReturn($location);
        $dynamicCode->method('getTargetUrl')->willReturn($targetUrl);
        $dynamicCode->method('getCreatedAt')->willReturn($createdAt);
        $dynamicCode->method('getUpdatedAt')->willReturn($updatedAt);

        $dynamicCodeList = new DynamicCodeList($this->createStub(DynamicCodeRepository::class));

        self::assertSame([
            'id' => $id,
            'code' => $code,
            'hits' => $hits,
            'companyName' => $companyName,
            'location' => $locationName,
            'targetUrl' => $targetUrl,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ], $dynamicCodeList->transform($dynamicCode, 0));
    }
}
