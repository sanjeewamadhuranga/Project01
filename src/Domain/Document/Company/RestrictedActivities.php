<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class RestrictedActivities
{
    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $gambling = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $weapons = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $drugs = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $prostitution = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $crypto = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $terrorist = null;

    public function getGambling(): ?bool
    {
        return $this->gambling;
    }

    public function setGambling(?bool $gambling): void
    {
        $this->gambling = $gambling;
    }

    public function getWeapons(): ?bool
    {
        return $this->weapons;
    }

    public function setWeapons(?bool $weapons): void
    {
        $this->weapons = $weapons;
    }

    public function getDrugs(): ?bool
    {
        return $this->drugs;
    }

    public function setDrugs(?bool $drugs): void
    {
        $this->drugs = $drugs;
    }

    public function getProstitution(): ?bool
    {
        return $this->prostitution;
    }

    public function setProstitution(?bool $prostitution): void
    {
        $this->prostitution = $prostitution;
    }

    public function getCrypto(): ?bool
    {
        return $this->crypto;
    }

    public function setCrypto(?bool $crypto): void
    {
        $this->crypto = $crypto;
    }

    public function getTerrorist(): ?bool
    {
        return $this->terrorist;
    }

    public function setTerrorist(?bool $terrorist): void
    {
        $this->terrorist = $terrorist;
    }
}
