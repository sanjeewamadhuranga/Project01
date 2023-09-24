<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Reports;

use App\Domain\Document\PayoutReport;
use App\Infrastructure\DataGrid\Reports\PayoutReportList;
use App\Infrastructure\Repository\PayoutReportRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class PayoutReportListTest extends UnitTestCase
{
    public function testItTransformsPayoutReportIntoArray(): void
    {
        $id = '61f021dc44ade122460c47b2';
        $generationDate = 'generation-date';
        $createdAt = new DateTime();
        $numberOfPayouts = new ArrayCollection([0, 1, 2]);
        $checksum = 500;

        $payoutReport = $this->createStub(PayoutReport::class);
        $payoutReport->method('getId')->willReturn($id);
        $payoutReport->method('getGenerationDate')->willReturn($generationDate);
        $payoutReport->method('getCreatedAt')->willReturn($createdAt);
        $payoutReport->method('getRemittanceIds')->willReturn($numberOfPayouts);
        $payoutReport->method('getTotalChecksum')->willReturn($checksum);

        $payoutReportList = new PayoutReportList($this->createStub(PayoutReportRepository::class));

        self::assertSame([
            'id' => $id,
            'generationDate' => $generationDate,
            'createdAt' => $createdAt,
            'numberOfPayouts' => count($numberOfPayouts),
            'checksum' => $checksum,
        ], $payoutReportList->transform($payoutReport, 0));
    }
}
