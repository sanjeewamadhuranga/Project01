<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Compliance;

use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\Pagination;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Transaction\CostStructure;
use App\Domain\Document\Transaction\InitiatorDetails;
use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\DataGrid\Compliance\CaseTransactionsList;
use App\Tests\Unit\UnitTestCase;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;

class CaseTransactionsListTest extends UnitTestCase
{
    public function testItTransformsTransactionIntoArray(): void
    {
        $id = '61f0213b6c0d85172231b503';
        $createdAt = new DateTime();
        $provider = 'some provider';
        $currency = 'GBP';
        $amount = 1500;
        $rate = 15;

        $transaction = $this->createStub(Transaction::class);
        $transaction->method('getId')->willReturn($id);
        $transaction->method('getCreatedAt')->willReturn($createdAt);
        $transaction->method('getProvider')->willReturn($provider);
        $transaction->method('getCurrency')->willReturn($currency);
        $transaction->method('getAmount')->willReturn($amount);
        $transaction->method('getRateFee')->willReturn($rate);

        $costFee = 9;
        $costCurrency = 'USD';
        $costPayable = 3;
        $costStructure = $this->createStub(CostStructure::class);
        $costStructure->method('getFee')->willReturn($costFee);
        $costStructure->method('getCurrency')->willReturn($costCurrency);
        $costStructure->method('getPayable')->willReturn($costPayable);
        $transaction->method('getCostStructure')->willReturn($costStructure);

        $initiatorEmail = 'initiator@pay.com';
        $initiatorName = 'Initiator Contact Name';
        $initiatorDetails = $this->createStub(InitiatorDetails::class);
        $initiatorDetails->method('getContactEmail')->willReturn($initiatorEmail);
        $initiatorDetails->method('getContactName')->willReturn($initiatorName);
        $transaction->method('getInitiatorDetails')->willReturn($initiatorDetails);

        $caseTransactionList = new CaseTransactionsList($this->createStub(DocumentManager::class), $this->createStub(PayoutBlock::class));

        self::assertSame([
            'id' => $id,
            'createdAt' => $createdAt,
            'provider' => $provider,
            'currency' => $currency,
            'amount' => $amount,
            'rate' => $rate,
            'commissionAmount' => $costFee,
            'costCurrency' => $costCurrency,
            'netAmount' => $costPayable,
            'initiatorEmail' => $initiatorEmail,
            'initiatorName' => $initiatorName,
        ], $caseTransactionList->transform($transaction, 0));
    }

    public function testItReturnsEmptyArrayWhenNoTransactions(): void
    {
        $payoutBlock = $this->createStub(PayoutBlock::class);
        $payoutBlock->method('getTransactions')->willReturn(new ArrayCollection());

        $caseTransactionList = new CaseTransactionsList($this->createStub(DocumentManager::class), $payoutBlock);
        $gridRequest = $this->createStub(GridRequest::class);
        $gridRequest->method('getPagination')->willReturn(new Pagination());

        self::assertSame([], $caseTransactionList->getData($gridRequest)->getItems());
    }
}
