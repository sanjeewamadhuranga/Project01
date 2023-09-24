<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\BankAccount\BankAccountStatus;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\HasLifecycleCallbacks]
#[MongoDB\EmbeddedDocument]
class BankAccount
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $name;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $currency;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $type = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $value1 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $value2 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $value3 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $value4 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $value5 = null;

    #[MongoDB\Field(enumType: BankAccountStatus::class)]
    protected BankAccountStatus $status;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $lastFourDigits;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $default = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $legacy;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getValue1(): ?string
    {
        return $this->value1;
    }

    public function setValue1(?string $value1): void
    {
        $this->value1 = $value1;
    }

    public function getValue2(): ?string
    {
        return $this->value2;
    }

    public function setValue2(?string $value2): void
    {
        $this->value2 = $value2;
    }

    public function getValue3(): ?string
    {
        return $this->value3;
    }

    public function setValue3(?string $value3): void
    {
        $this->value3 = $value3;
    }

    public function getValue4(): ?string
    {
        return $this->value4;
    }

    public function setValue4(?string $value4): void
    {
        $this->value4 = $value4;
    }

    public function getValue5(): ?string
    {
        return $this->value5;
    }

    public function setValue5(?string $value5): void
    {
        $this->value5 = $value5;
    }

    public function getStatus(): BankAccountStatus
    {
        return $this->status;
    }

    public function setStatus(BankAccountStatus $status): void
    {
        $this->status = $status;
    }

    public function getLastFourDigits(): string
    {
        return $this->lastFourDigits;
    }

    #[MongoDB\PreUpdate]
    #[MongoDB\PrePersist]
    public function setLastFourDigits(): void
    {
        $valueForFourDigits = $this->getValueForFourDigits();
        $this->lastFourDigits = null === $valueForFourDigits ? '' : substr($valueForFourDigits, -4, 4);
    }

    private function getValueForFourDigits(): ?string
    {
        if (null !== $this->value2 && '' !== $this->value2) {
            return $this->value2;
        }

        if (null !== $this->value3 && '' !== $this->value3) {
            return $this->value3;
        }

        if (null !== $this->value4 && '' !== $this->value4) {
            return $this->value4;
        }

        if (null !== $this->value5 && '' !== $this->value5) {
            return $this->value5;
        }

        return null;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    public function isLegacy(): bool
    {
        return $this->legacy;
    }

    public function setLegacy(bool $legacy): void
    {
        $this->legacy = $legacy;
    }
}
