<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Document\FX\FxOrder;
use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\DataGrid\FxOrderList;
use App\Infrastructure\Repository\Transaction\FxOrderRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;

class FxOrderListTest extends UnitTestCase
{
    public function testItTransformsFxOrderIntoArray(): void
    {
        $transactionId = 'abg5-gsdf-ypo4-fo65';
        $transactionAmount = 70000;

        $transaction = $this->createStub(Transaction::class);
        $transaction->method('getId')->willReturn($transactionId);
        $transaction->method('getAmount')->willReturn($transactionAmount);

        $id = '61f021a163b571290822cddd';
        $createdAt = new DateTime();
        $payAmount = 50000;
        $marketAmount = 60000.0;
        $profitAmount = 10000.0;
        $currency = 'EURO';
        $counterCurrency = 'counter-currency';
        $merchantRebatePercentage = 3.5;
        $merchantRebateAmount = 3000.0;
        $currencyPair = 'currency-pair';
        $marketRate = 13.0;
        $reference = 'reference';
        $positionReference = 'position-reference';
        $markupRate = 2.5;

        $fxOrder = $this->createStub(FxOrder::class);

        $fxOrderList = new FxOrderList($this->createStub(FxOrderRepository::class));
        $fxOrder->method('getId')->willReturn($id);
        $fxOrder->method('getCreatedAt')->willReturn($createdAt);
        $fxOrder->method('getTransaction')->willReturn($transaction);
        $fxOrder->method('getPayAmount')->willReturn($payAmount);
        $fxOrder->method('getMarketAmount')->willReturn($marketAmount);
        $fxOrder->method('getProfitAmount')->willReturn($profitAmount);
        $fxOrder->method('getCurrency')->willReturn($currency);
        $fxOrder->method('getCounterCurrency')->willReturn($counterCurrency);
        $fxOrder->method('getMerchantRebatePercentage')->willReturn($merchantRebatePercentage);
        $fxOrder->method('getMerchantRebateAmount')->willReturn($merchantRebateAmount);
        $fxOrder->method('getCurrencyPair')->willReturn($currencyPair);
        $fxOrder->method('getMarketRate')->willReturn($marketRate);
        $fxOrder->method('getReference')->willReturn($reference);
        $fxOrder->method('getPositionReference')->willReturn($positionReference);
        $fxOrder->method('getMarkupRate')->willReturn($markupRate);

        self::assertSame([
            'id' => $id,
            'created' => $createdAt,
            'transactionId' => $transactionId,
            'transactionAmount' => $transactionAmount,
            'payAmount' => $payAmount,
            'marketAmount' => $marketAmount,
            'profitAmount' => $profitAmount,
            'currency' => $currency,
            'counterCurrency' => $counterCurrency,
            'merchantRebatePercentage' => $merchantRebatePercentage,
            'merchantRebateAmount' => $merchantRebateAmount,
            'currencyPair' => $currencyPair,
            'marketRate' => $marketRate,
            'reference' => $reference,
            'positionReference' => $positionReference,
            'markupRate' => $markupRate,
        ], $fxOrderList->transform($fxOrder, 0));
    }
}
