<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Company\ComplianceOutcome;
use App\Domain\Company\ComplianceStatus;
use App\Domain\Document\BaseDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class ComplianceReport extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $userId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $companyId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $applicantId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $checkId = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $mustComplete = true;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $completed = false;

    #[MongoDB\Field(enumType: ComplianceStatus::class)]
    protected ComplianceStatus $state = ComplianceStatus::PENDING;

    #[MongoDB\Field(enumType: ComplianceOutcome::class, nullable: true)]
    protected ?ComplianceOutcome $result = null;

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getApplicantId(): string
    {
        return $this->applicantId;
    }

    public function setApplicantId(string $applicantId): void
    {
        $this->applicantId = $applicantId;
    }

    public function getCheckId(): ?string
    {
        return $this->checkId;
    }

    public function setCheckId(?string $checkId): void
    {
        $this->checkId = $checkId;
    }

    public function isMustComplete(): bool
    {
        return $this->mustComplete;
    }

    public function setMustComplete(bool $mustComplete): void
    {
        $this->mustComplete = $mustComplete;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    public function setCompanyId(string $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function getState(): ComplianceStatus
    {
        return $this->state;
    }

    public function setState(ComplianceStatus $state): void
    {
        $this->state = $state;
    }

    public function getResult(): ?ComplianceOutcome
    {
        return $this->result;
    }

    public function setResult(?ComplianceOutcome $result): void
    {
        $this->result = $result;
    }

    public function isPending(): bool
    {
        return ComplianceStatus::PENDING === $this->state;
    }

    public function isOutComeClear(): bool
    {
        return ComplianceOutcome::CLEAR === $this->result;
    }

    public function isOutComeConsider(): bool
    {
        return ComplianceOutcome::CONSIDER === $this->result;
    }
}
