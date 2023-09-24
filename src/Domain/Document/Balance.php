<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Infrastructure\Repository\BalanceRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'balance', repositoryClass: BalanceRepository::class, readOnly: true)]
class Balance extends BaseDocument implements CompanyAware
{
    #[MongoDB\ReferenceOne(name: 'companyId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected Company $company;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $credit = 0;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $debit = 0;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $calculatedBalance = 0;

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getCredit(): int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): void
    {
        $this->credit = $credit;
    }

    public function getDebit(): int
    {
        return $this->debit;
    }

    public function setDebit(int $debit): void
    {
        $this->debit = $debit;
    }

    public function getCalculatedBalance(): int
    {
        return $this->calculatedBalance;
    }

    public function setCalculatedBalance(int $calculatedBalance): void
    {
        $this->calculatedBalance = $calculatedBalance;
    }
}
