<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class RiskInformation
{
    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::HASH)]
    protected ?array $previousProviders = null;

    #[MongoDB\Field(type: MongoDBType::FLOAT)]
    protected ?float $averageTransaction = null;

    #[MongoDB\Field(type: MongoDBType::FLOAT)]
    protected ?float $averageMonthlyTransactionVolume = null;

    #[MongoDB\Field(type: MongoDBType::FLOAT)]
    protected ?float $maxTransaction = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $businessInformation = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $currentlyTakesPayments = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $previouslyTakesPayments = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $previouslyExperiencedFraud = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $businessOperatingSince = null;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::COLLECTION)]
    protected array $currentMarketingStrategy = [];

    /**
     * @return string[]
     */
    public function getPreviousProviders(): ?array
    {
        return $this->previousProviders;
    }

    /**
     * @param string[] $previousProviders
     */
    public function setPreviousProviders(?array $previousProviders): void
    {
        $this->previousProviders = $previousProviders;
    }

    public function getAverageTransaction(): ?float
    {
        return $this->averageTransaction;
    }

    public function setAverageTransaction(?float $averageTransaction): void
    {
        $this->averageTransaction = $averageTransaction;
    }

    public function getAverageMonthlyTransactionVolume(): ?float
    {
        return $this->averageMonthlyTransactionVolume;
    }

    public function setAverageMonthlyTransactionVolume(?float $averageMonthlyTransactionVolume): void
    {
        $this->averageMonthlyTransactionVolume = $averageMonthlyTransactionVolume;
    }

    public function getMaxTransaction(): ?float
    {
        return $this->maxTransaction;
    }

    public function setMaxTransaction(?float $maxTransaction): void
    {
        $this->maxTransaction = $maxTransaction;
    }

    public function getBusinessInformation(): ?string
    {
        return $this->businessInformation;
    }

    public function setBusinessInformation(?string $businessInformation): void
    {
        $this->businessInformation = $businessInformation;
    }

    public function getCurrentlyTakesPayments(): ?bool
    {
        return $this->currentlyTakesPayments;
    }

    public function setCurrentlyTakesPayments(?bool $currentlyTakesPayments): void
    {
        $this->currentlyTakesPayments = $currentlyTakesPayments;
    }

    public function getPreviouslyTakesPayments(): ?bool
    {
        return $this->previouslyTakesPayments;
    }

    public function setPreviouslyTakesPayments(?bool $previouslyTakesPayments): void
    {
        $this->previouslyTakesPayments = $previouslyTakesPayments;
    }

    public function getPreviouslyExperiencedFraud(): ?bool
    {
        return $this->previouslyExperiencedFraud;
    }

    public function setPreviouslyExperiencedFraud(?bool $previouslyExperiencedFraud): void
    {
        $this->previouslyExperiencedFraud = $previouslyExperiencedFraud;
    }

    public function getBusinessOperatingSince(): ?string
    {
        return $this->businessOperatingSince;
    }

    public function setBusinessOperatingSince(?string $businessOperatingSince): void
    {
        $this->businessOperatingSince = $businessOperatingSince;
    }

    /**
     * @return string[]
     */
    public function getCurrentMarketingStrategy(): array
    {
        return $this->currentMarketingStrategy;
    }

    /**
     * @param string[] $currentMarketingStrategy
     */
    public function setCurrentMarketingStrategy(array $currentMarketingStrategy): void
    {
        $this->currentMarketingStrategy = $currentMarketingStrategy;
    }
}
