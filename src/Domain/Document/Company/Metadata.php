<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class Metadata
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $connectedBusiness = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $subsidiariesAffiliates = null;

    #[MongoDB\EmbedOne(targetDocument: BusinessIncome::class)]
    protected ?BusinessIncome $income = null;

    #[MongoDB\EmbedOne(targetDocument: RestrictedActivities::class)]
    protected ?RestrictedActivities $restrictedActivities = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $businessRegistrationDate = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $addressValidation = null;

    public function getConnectedBusiness(): ?string
    {
        return $this->connectedBusiness;
    }

    public function setConnectedBusiness(?string $connectedBusiness): void
    {
        $this->connectedBusiness = $connectedBusiness;
    }

    public function getSubsidiariesAffiliates(): ?string
    {
        return $this->subsidiariesAffiliates;
    }

    public function setSubsidiariesAffiliates(?string $subsidiariesAffiliates): void
    {
        $this->subsidiariesAffiliates = $subsidiariesAffiliates;
    }

    public function getIncome(): ?BusinessIncome
    {
        return $this->income;
    }

    public function setIncome(?BusinessIncome $income): void
    {
        $this->income = $income;
    }

    public function getRestrictedActivities(): ?RestrictedActivities
    {
        return $this->restrictedActivities;
    }

    public function setRestrictedActivities(?RestrictedActivities $restrictedActivities): void
    {
        $this->restrictedActivities = $restrictedActivities;
    }

    public function getBusinessRegistrationDate(): ?string
    {
        return $this->businessRegistrationDate;
    }

    public function setBusinessRegistrationDate(?string $businessRegistrationDate): void
    {
        $this->businessRegistrationDate = $businessRegistrationDate;
    }

    public function getAddressValidation(): ?bool
    {
        return $this->addressValidation;
    }

    public function setAddressValidation(?bool $addressValidation): void
    {
        $this->addressValidation = $addressValidation;
    }
}
