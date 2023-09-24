<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Compliance;

use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\DataGrid\SimpleMongoDataGrid;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * @extends SimpleMongoDataGrid<Transaction>
 */
class CaseTransactionsList extends SimpleMongoDataGrid
{
    public function __construct(DocumentManager $documentManager, private readonly PayoutBlock $payoutBlock)
    {
        parent::__construct($documentManager);
    }

    /**
     * @param Transaction $item
     */
    public function transform(mixed $item, int|string $index): mixed
    {
        return [
            'id' => $item->getId(),
            'createdAt' => $item->getCreatedAt(),
            'provider' => $item->getProvider(),
            'currency' => $item->getCurrency(),
            'amount' => $item->getAmount(),
            'rate' => $item->getRateFee(),
            'commissionAmount' => $item->getCostStructure()?->getFee(),
            'costCurrency' => $item->getCostStructure()?->getCurrency(),
            'netAmount' => $item->getCostStructure()?->getPayable(),
            'initiatorEmail' => $item->getInitiatorDetails()?->getContactEmail(),
            'initiatorName' => $item->getInitiatorDetails()?->getContactName(),
        ];
    }

    protected function getItemType(): string
    {
        return Transaction::class;
    }

    /**
     * @return iterable<int, Transaction>
     */
    protected function getItems(): iterable
    {
        if (0 === count($this->payoutBlock->getTransactions())) {
            return [];
        }

        return $this->documentManager->createQueryBuilder(static::getItemType())
            ->field('deleted')
            ->notEqual(true) // using notEqual in case field is missing from the document
            ->field('id')->in($this->payoutBlock->getTransactions()->map(fn (Transaction $transaction) => $transaction->getId())->getValues())
            ->getQuery()
        ;
    }
}
