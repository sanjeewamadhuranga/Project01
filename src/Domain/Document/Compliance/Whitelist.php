<?php

declare(strict_types=1);

namespace App\Domain\Document\Compliance;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Security\Administrator;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class Whitelist extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected DateTimeInterface $expires;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $reason;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected DateTimeInterface $updated;

    #[MongoDB\ReferenceOne(targetDocument: Administrator::class)]
    protected Administrator $user;

    public function getExpires(): DateTimeInterface
    {
        return $this->expires;
    }

    public function setExpires(DateTimeInterface $expires): void
    {
        $this->expires = $expires;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function getUpdated(): DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(DateTimeInterface $updated): void
    {
        $this->updated = $updated;
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
