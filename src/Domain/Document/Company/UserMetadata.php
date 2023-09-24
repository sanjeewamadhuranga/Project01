<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class UserMetadata
{
    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $politicallyExposed = false;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $politicalType = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $politicalName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $politicalDesignation = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $politicalRelationship = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $socialLink = null;

    public function getPoliticallyExposed(): ?bool
    {
        return $this->politicallyExposed;
    }

    public function setPoliticallyExposed(?bool $politicallyExposed): void
    {
        $this->politicallyExposed = $politicallyExposed;
    }

    public function getPoliticalType(): ?string
    {
        return $this->politicalType;
    }

    public function setPoliticalType(?string $politicalType): void
    {
        $this->politicalType = $politicalType;
    }

    public function getPoliticalName(): ?string
    {
        return $this->politicalName;
    }

    public function setPoliticalName(?string $politicalName): void
    {
        $this->politicalName = $politicalName;
    }

    public function getPoliticalDesignation(): ?string
    {
        return $this->politicalDesignation;
    }

    public function setPoliticalDesignation(?string $politicalDesignation): void
    {
        $this->politicalDesignation = $politicalDesignation;
    }

    public function getPoliticalRelationship(): ?string
    {
        return $this->politicalRelationship;
    }

    public function setPoliticalRelationship(?string $politicalRelationship): void
    {
        $this->politicalRelationship = $politicalRelationship;
    }

    public function getSocialLink(): ?string
    {
        return $this->socialLink;
    }

    public function setSocialLink(?string $socialLink): void
    {
        $this->socialLink = $socialLink;
    }
}
