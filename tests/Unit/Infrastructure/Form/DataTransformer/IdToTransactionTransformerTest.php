<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\DataTransformer;

use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\Form\DataTransformer\IdToTransactionTransformer;
use App\Infrastructure\Repository\Transaction\TransactionRepository;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdToTransactionTransformerTest extends UnitTestCase
{
    public function testItTransformsTransactionToId(): void
    {
        $transactionId = 'ag5t-yoy5-hps4-y0y0';

        $transaction = $this->createStub(Transaction::class);
        $transaction->method('getId')->willReturn($transactionId);

        $transformer = new IdToTransactionTransformer($this->createStub(TransactionRepository::class));

        self::assertSame($transactionId, $transformer->transform($transaction));
    }

    public function testItDoesNotTransformOtherObjects(): void
    {
        $transformer = new IdToTransactionTransformer($this->createStub(TransactionRepository::class));

        self::assertNull($transformer->transform(null));
    }

    public function testItTransformsIdToTransaction(): void
    {
        $transaction = $this->createStub(Transaction::class);

        $repository = $this->createStub(TransactionRepository::class);
        $repository->method('find')->willReturn($transaction);

        $transformer = new IdToTransactionTransformer($repository);

        self::assertSame($transaction, $transformer->reverseTransform('get5-gty6-jyo5-ad5a'));
    }

    public function testItThrowsExceptionWhenNoTransactionForIdFound(): void
    {
        $id = '61f021dc44ade122460c47b5';
        $repository = $this->createStub(TransactionRepository::class);
        $repository->method('find')->willReturn(null);

        self::expectException(TransformationFailedException::class);
        self::expectExceptionMessage(sprintf('A transaction with id "%s" does not exist!', $id));

        (new IdToTransactionTransformer($repository))->reverseTransform($id);
    }
}
