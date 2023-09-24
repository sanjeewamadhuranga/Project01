<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Document\TransactionReport;
use App\Infrastructure\DataGrid\TransactionReportList;
use App\Infrastructure\Repository\TransactionReportRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;

class TransactionReportListTest extends UnitTestCase
{
    public function testItTransformsTransactionReportIntoArray(): void
    {
        $id = '61f021dc44ade122460c47b4';
        $createdAt = new DateTime();
        $generationDate = 'generation-date';
        $totalChecksum = 523;
        $totalNumberOfTransactions = 53;

        $transactionReport = $this->createStub(TransactionReport::class);
        $transactionReport->method('getId')->willReturn($id);
        $transactionReport->method('getCreatedAt')->willReturn($createdAt);
        $transactionReport->method('getGenerationDate')->willReturn($generationDate);
        $transactionReport->method('getTotalChecksum')->willReturn($totalChecksum);
        $transactionReport->method('getTotalNumberOfTransactions')->willReturn($totalNumberOfTransactions);

        $transactionReportList = new TransactionReportList($this->createStub(TransactionReportRepository::class));

        self::assertSame([
            'id' => $id,
            'createdAt' => $createdAt,
            'generationDate' => $generationDate,
            'totalChecksum' => $totalChecksum,
            'totalNumberOfTransactions' => $totalNumberOfTransactions,
        ], $transactionReportList->transform($transactionReport, 0));
    }
}
