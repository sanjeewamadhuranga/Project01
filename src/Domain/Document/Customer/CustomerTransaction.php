<?php

declare(strict_types=1);

namespace App\Domain\Document\Customer;

use App\Domain\Document\Traits\HasId;
use App\Domain\Transaction\Status;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class CustomerTransaction
{
    use HasId;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $amount = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $currency = null;

    #[MongoDB\Field(enumType: Status::class)]
    protected Status $state;

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): ?int
    {
        return $this->currency;
    }

    public function setCurrency(?int $currency): void
    {
        $this->currency = $currency;
    }

    public function getState(): Status
    {
        return $this->state;
    }

    public function setState(Status $state): void
    {
        $this->state = $state;
    }
}
