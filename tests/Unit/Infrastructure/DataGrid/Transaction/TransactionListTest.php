<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Transaction;

use App\Domain\Document\Transaction\Transaction;
use App\Domain\Transformer\TransactionTransformer;
use App\Infrastructure\DataGrid\Transaction\TransactionList;
use App\Infrastructure\Repository\Transaction\TransactionRepository;
use App\Tests\Unit\UnitTestCase;

class TransactionListTest extends UnitTestCase
{
    public function testItUsesTransformerToTransformPlatformBillingReportToArray(): void
    {
        $transaction = $this->createStub(Transaction::class);

        $transactionTransformer = $this->createMock(TransactionTransformer::class);
        $transactionTransformer->expects(self::once())->method('transform')->with($transaction);

        $transactionList = new TransactionList($this->createStub(TransactionRepository::class), $transactionTransformer);
        $transactionList->transform($transaction, 0);
    }
}
