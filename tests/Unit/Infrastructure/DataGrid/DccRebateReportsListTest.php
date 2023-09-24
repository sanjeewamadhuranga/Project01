<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Document\FX\DccRebateReport;
use App\Infrastructure\DataGrid\DccRebateReportsList;
use App\Infrastructure\Repository\FX\DccRebateReportRepository;
use App\Tests\Unit\UnitTestCase;

class DccRebateReportsListTest extends UnitTestCase
{
    public function testItTransformsDccRebateReportIntoArray(): void
    {
        $id = '61f021a163b571290822cddb';
        $generationDate = 'generation-date';

        $dccRebateReport = $this->createStub(DccRebateReport::class);
        $dccRebateReport->method('getId')->willReturn($id);
        $dccRebateReport->method('getGenerationDate')->willReturn($generationDate);

        $dccRebateReportList = new DccRebateReportsList($this->createStub(DccRebateReportRepository::class));

        self::assertSame([
            'id' => $id,
            'generationDate' => $generationDate,
        ], $dccRebateReportList->transform($dccRebateReport, 0));
    }
}
