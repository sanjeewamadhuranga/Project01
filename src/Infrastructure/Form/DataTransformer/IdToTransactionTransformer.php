<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\DataTransformer;

use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\Repository\Transaction\TransactionRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<Transaction, string>
 *
 * @todo could be replaced with IdToDocumentTransformer
 */
class IdToTransactionTransformer implements DataTransformerInterface
{
    public function __construct(private readonly TransactionRepository $transactionRepository)
    {
    }

    public function transform(mixed $transaction): ?string
    {
        return $transaction instanceof Transaction ? $transaction->getId() : null;
    }

    public function reverseTransform(mixed $id): ?Transaction
    {
        $transaction = $this->transactionRepository->find($id);

        if (null === $transaction) {
            throw new TransformationFailedException(sprintf('A transaction with id "%s" does not exist!', $id));
        }

        return $transaction;
    }
}
