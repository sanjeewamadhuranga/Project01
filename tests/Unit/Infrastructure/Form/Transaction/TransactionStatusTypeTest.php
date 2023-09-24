<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Transaction;

use App\Domain\Document\Transaction\Transaction;
use App\Domain\Transaction\Status;
use App\Domain\Transaction\TransactionStatusRequest;
use App\Infrastructure\Form\Transaction\TransactionStatusType;
use Symfony\Component\Form\Test\TypeTestCase;

class TransactionStatusTypeTest extends TypeTestCase
{
    public function testItShowsRefundAmountIfNewFlowIsEnabled(): void
    {
        $transaction = $this->getTransaction(Status::CONFIRMED);
        $form = $this->factory->create(TransactionStatusType::class, new TransactionStatusRequest($transaction, true));

        self::assertTrue($form->has('refundAmount'));
        self::assertSame($transaction->getCurrency(), $form->get('refundAmount')->getConfig()->getOption('currency'));
    }

    public function testItDoesNotShowRefundAmountIfNewFlowIsDisabled(): void
    {
        $transaction = $this->getTransaction(Status::CONFIRMED);
        $form = $this->factory->create(TransactionStatusType::class, new TransactionStatusRequest($transaction, false));

        self::assertFalse($form->has('refundAmount'));
    }

    /**
     * @dataProvider transactionStatusProvider
     *
     * @param Status[] $availableOptions
     */
    public function testItShowsStatusOptionsDependingOnCurrentTransactionState(Status $status, array $availableOptions, bool $newFlow = true): void
    {
        $transaction = $this->getTransaction($status);
        $form = $this->factory->create(TransactionStatusType::class, new TransactionStatusRequest($transaction, $newFlow));

        self::assertTrue($form->has('state'));
        self::assertSame($availableOptions, $form->get('state')->getConfig()->getOption('choices'));
    }

    /**
     * @return iterable<string, array{Status, Status[]}|array{Status, Status[], bool}>
     */
    public function transactionStatusProvider(): iterable
    {
        yield 'CONFIRMED' => [
            Status::CONFIRMED,
            [
                Status::CONFIRMED,
                Status::REFUND_REQUESTED,
            ],
        ];

        yield 'REFUND_REQUESTED with new flow' => [
            Status::REFUND_REQUESTED,
            [
                Status::REFUND_REQUESTED,
                Status::REFUNDED,
            ],
        ];

        yield 'REFUND_REQUESTED with old flow' => [
            Status::REFUND_REQUESTED,
            [
                Status::REFUND_REQUESTED,
                Status::REFUNDED,
                Status::CONFIRMED,
            ],
            false,
        ];

        yield 'REFUNDED' => [
            Status::REFUNDED,
            [
                Status::REFUNDED,
            ],
        ];

        yield 'AUTHORIZED' => [
            Status::AUTHORIZED,
            [
                Status::QR_CODE_GENERATED,
                Status::AUTHORIZED,
                Status::CONFIRMED,
                Status::CANCELLED,
                Status::VOIDED,
                Status::REFUND_REQUESTED,
                Status::REFUNDED,
            ],
        ];
    }

    private function getTransaction(Status $status, int $balance = 100, string $currency = 'SGD'): Transaction
    {
        $transaction = $this->createStub(Transaction::class);
        $transaction->method('getCurrency')->willReturn($currency);
        $transaction->method('getState')->willReturn($status);
        $transaction->method('getAvailableBalance')->willReturn($balance);

        return $transaction;
    }
}
