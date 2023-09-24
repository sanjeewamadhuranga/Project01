<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Accounting\AccountingStatusType;
use App\Domain\Document\FX\Settlement;
use App\Infrastructure\DataGrid\FxSettlementList;
use App\Infrastructure\Repository\FX\SettlementRepository;
use App\Tests\Unit\UnitTestCase;

class FxSettlementListTest extends UnitTestCase
{
    public function testItTransformsSettlementIntoArray(): void
    {
        $id = '61f021a163b571290822cdde';
        $createdAt = 'created-at';
        $settlementDateTime = 'settlementDateTime';
        $settlementStatus = 'some-status';
        $kantoxOrderRef = 'kantox-order-ref';
        $payInAmount = 500.5;
        $currency = 'GPB';
        $accountingStatus = AccountingStatusType::EXECUTED;

        $settlement = $this->createStub(Settlement::class);
        $settlement->method('getId')->willReturn($id);
        $settlement->method('getCreatedTimeStamp')->willReturn($createdAt);
        $settlement->method('getSettlementDateTime')->willReturn($settlementDateTime);
        $settlement->method('getSettlementStatus')->willReturn($settlementStatus);
        $settlement->method('getKantoxOrderRef')->willReturn($kantoxOrderRef);
        $settlement->method('getPayInAmount')->willReturn($payInAmount);
        $settlement->method('getPayIncurrency')->willReturn($currency);
        $settlement->method('getAccountingStatus')->willReturn($accountingStatus);

        $fxSettlementList = new FxSettlementList($this->createStub(SettlementRepository::class));

        self::assertSame([
            'id' => $id,
            'createdAt' => $createdAt,
            'settlementDateTime' => $settlementDateTime,
            'settlementStatus' => $settlementStatus,
            'kantoxOrderRef' => $kantoxOrderRef,
            'payInAmount' => $payInAmount,
            'payInCurrency' => $currency,
            'accountingStatus' => $accountingStatus,
        ], $fxSettlementList->transform($settlement, 0));
    }
}
