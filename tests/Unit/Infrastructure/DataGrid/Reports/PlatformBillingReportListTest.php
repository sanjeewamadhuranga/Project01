<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Reports;

use App\Domain\Document\PlatformBillingReport\PlatFormBillingReport;
use App\Infrastructure\DataGrid\Reports\PlatformBillingReportList;
use App\Infrastructure\Repository\PlatformBillingReportRepository;
use App\Tests\Unit\UnitTestCase;

class PlatformBillingReportListTest extends UnitTestCase
{
    public function testItTransformsPlatformBillingReportIntoArray(): void
    {
        $id = '61f021dc44ade122460c47b3';
        $generationDate = 'generation date';

        $platformBillingReport = $this->createStub(PlatFormBillingReport::class);
        $platformBillingReport->method('getId')->willReturn($id);
        $platformBillingReport->method('getGenerationDate')->willReturn($generationDate);

        $platformBillingReportList = new PlatformBillingReportList($this->createStub(PlatformBillingReportRepository::class));

        self::assertSame([
            'id' => $id,
            'generationDate' => $generationDate,
        ], $platformBillingReportList->transform($platformBillingReport, 0));
    }
}
