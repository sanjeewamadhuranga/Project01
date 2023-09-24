<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Document\Security\Administrator;
use DateTime;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class ProviderOnboardingAttempt
{
    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected DateTimeInterface $dateRequested;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $result;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $reason = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $onboardingRequest;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $onboardingResponse;

    #[MongoDB\ReferenceOne(targetDocument: Administrator::class)]
    protected Administrator $user;

    public function __construct()
    {
        $this->dateRequested = new DateTime();
    }

    public function getDateRequested(): DateTimeInterface
    {
        return $this->dateRequested;
    }

    public function setDateRequested(DateTimeInterface $dateRequested): void
    {
        $this->dateRequested = $dateRequested;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function getOnboardingRequest(): string
    {
        return $this->onboardingRequest;
    }

    public function setOnboardingRequest(string $onboardingRequest): void
    {
        $this->onboardingRequest = $onboardingRequest;
    }

    public function getOnboardingResponse(): string
    {
        return $this->onboardingResponse;
    }

    public function setOnboardingResponse(string $onboardingResponse): void
    {
        $this->onboardingResponse = $onboardingResponse;
    }

    public function getUser(): Administrator
    {
        return $this->user;
    }

    public function setUser(Administrator $user): void
    {
        $this->user = $user;
    }
}
