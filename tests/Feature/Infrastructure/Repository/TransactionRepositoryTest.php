<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\Repository;

use App\Domain\Document\Company\Company;
use App\Infrastructure\Repository\Transaction\TransactionRepository;
use App\Tests\Feature\BaseTestCase;

class TransactionRepositoryTest extends BaseTestCase
{
    private TransactionRepository $repository;

    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(TransactionRepository::class);
        $this->company = $this->refresh($this->getTestCompany());
    }

    public function testItCalculatesCreditAndPendingFundsForCompany(): void
    {
        $stats = $this->repository->getCreditAndPendingFunds($this->company);
        self::assertSame(6_080_000, $stats->pendingFunds);
        self::assertSame(9000, $stats->credit);
    }

    public function testItCalculatesCreditAndPendingFundsForCompany1(): void
    {
        $stats = $this->repository->getConfirmedTransactionAmountsWithCurrency([$this->company], 'GBP');
        self::assertSame(105000, $stats->amount);
        self::assertSame(4, $stats->count);
        self::assertSame(26250, $stats->avg);
        self::assertSame('GBP', $stats->currency);
    }
}
