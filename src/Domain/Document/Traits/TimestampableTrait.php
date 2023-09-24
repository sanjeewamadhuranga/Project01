<?php

declare(strict_types=1);

namespace App\Domain\Document\Traits;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait TimestampableTrait
{
    #[Gedmo\Timestampable(on: 'create')]
    #[MongoDB\Field(type: MongoDBType::DATE)]
    #[MongoDB\AlsoLoad(value: 'created')]
    #[Groups(['read'])]
    protected ?DateTimeInterface $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[MongoDB\Field(type: MongoDBType::DATE)]
    #[MongoDB\AlsoLoad(value: 'updated')]
    #[Groups(['read'])]
    protected ?DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
