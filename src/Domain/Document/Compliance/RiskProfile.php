<?php

declare(strict_types=1);

namespace App\Domain\Document\Compliance;

use App\Domain\Document\BaseDocument;
use App\Infrastructure\Repository\RiskProfileRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'compliance_riskprofiles', repositoryClass: RiskProfileRepository::class)]
class RiskProfile extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $code = '';

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $currency = '';

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $dailyAmount = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $weeklyAmount = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $monthlyAmount = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $duplicateBuyerId = false;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $duplicateBuyerIdTimeFrame = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $duplicateAmount = false;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $duplicateAmountTimeFrame = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $singleTransactionAmount = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $numberOfConfirmedTransactions = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $numberOfConfirmedTransactionsTimeFrame = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $allowedTimeIntervalStart = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $allowedTimeIntervalEnd = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getDailyAmount(): ?int
    {
        return $this->dailyAmount;
    }

    public function setDailyAmount(int $dailyAmount): void
    {
        $this->dailyAmount = $dailyAmount;
    }

    public function getWeeklyAmount(): ?int
    {
        return $this->weeklyAmount;
    }

    public function setWeeklyAmount(int $weeklyAmount): void
    {
        $this->weeklyAmount = $weeklyAmount;
    }

    public function getMonthlyAmount(): ?int
    {
        return $this->monthlyAmount;
    }

    public function setMonthlyAmount(int $monthlyAmount): void
    {
        $this->monthlyAmount = $monthlyAmount;
    }

    public function isDuplicateBuyerId(): bool
    {
        return $this->duplicateBuyerId;
    }

    public function setDuplicateBuyerId(bool $duplicateBuyerId): void
    {
        $this->duplicateBuyerId = $duplicateBuyerId;
    }

    public function getDuplicateBuyerIdTimeFrame(): ?int
    {
        return $this->duplicateBuyerIdTimeFrame;
    }

    public function setDuplicateBuyerIdTimeFrame(int $duplicateBuyerIdTimeFrame): void
    {
        $this->duplicateBuyerIdTimeFrame = $duplicateBuyerIdTimeFrame;
    }

    public function isDuplicateAmount(): bool
    {
        return $this->duplicateAmount;
    }

    public function setDuplicateAmount(bool $duplicateAmount): void
    {
        $this->duplicateAmount = $duplicateAmount;
    }

    public function getDuplicateAmountTimeFrame(): ?int
    {
        return $this->duplicateAmountTimeFrame;
    }

    public function setDuplicateAmountTimeFrame(int $duplicateAmountTimeFrame): void
    {
        $this->duplicateAmountTimeFrame = $duplicateAmountTimeFrame;
    }

    public function getSingleTransactionAmount(): ?int
    {
        return $this->singleTransactionAmount;
    }

    public function setSingleTransactionAmount(int $singleTransactionAmount): void
    {
        $this->singleTransactionAmount = $singleTransactionAmount;
    }

    public function getNumberOfConfirmedTransactions(): ?int
    {
        return $this->numberOfConfirmedTransactions;
    }

    public function setNumberOfConfirmedTransactions(int $numberOfConfirmedTransactions): void
    {
        $this->numberOfConfirmedTransactions = $numberOfConfirmedTransactions;
    }

    public function getNumberOfConfirmedTransactionsTimeFrame(): ?int
    {
        return $this->numberOfConfirmedTransactionsTimeFrame;
    }

    public function setNumberOfConfirmedTransactionsTimeFrame(int $numberOfConfirmedTransactionsTimeFrame): void
    {
        $this->numberOfConfirmedTransactionsTimeFrame = $numberOfConfirmedTransactionsTimeFrame;
    }

    public function getAllowedTimeIntervalStart(): ?string
    {
        return $this->allowedTimeIntervalStart;
    }

    public function setAllowedTimeIntervalStart(string $allowedTimeIntervalStart): void
    {
        $this->allowedTimeIntervalStart = $allowedTimeIntervalStart;
    }

    public function getAllowedTimeIntervalEnd(): ?string
    {
        return $this->allowedTimeIntervalEnd;
    }

    public function setAllowedTimeIntervalEnd(string $allowedTimeIntervalEnd): void
    {
        $this->allowedTimeIntervalEnd = $allowedTimeIntervalEnd;
    }
}
