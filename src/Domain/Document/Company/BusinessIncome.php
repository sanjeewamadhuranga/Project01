<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class BusinessIncome
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $incomeSource = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $expectedTurnOver = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $avgTransaction = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $maxTransaction = null;

    public function getIncomeSource(): ?string
    {
        return $this->incomeSource;
    }

    public function setIncomeSource(?string $incomeSource): void
    {
        $this->incomeSource = $incomeSource;
    }

    public function getExpectedTurnOver(): ?string
    {
        return $this->expectedTurnOver;
    }

    public function setExpectedTurnOver(?string $expectedTurnOver): void
    {
        $this->expectedTurnOver = $expectedTurnOver;
    }

    public function getAvgTransaction(): ?string
    {
        return $this->avgTransaction;
    }

    public function setAvgTransaction(?string $avgTransaction): void
    {
        $this->avgTransaction = $avgTransaction;
    }

    public function getMaxTransaction(): ?string
    {
        return $this->maxTransaction;
    }

    public function setMaxTransaction(?string $maxTransaction): void
    {
        $this->maxTransaction = $maxTransaction;
    }
}
