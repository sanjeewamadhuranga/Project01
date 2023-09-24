<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Settings\Theme;
use App\Infrastructure\Form\Company\Address\AddressType;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\EmbeddedDocument]
class Address implements Stringable
{
    #[MongoDB\Field(name: 'flat_number', type: MongoDBType::STRING)]
    protected ?string $flatNumber = null;

    #[MongoDB\Field(name: 'building_number', type: MongoDBType::STRING)]
    protected ?string $buildingNumber = null;

    #[MongoDB\Field(name: 'building_name', type: MongoDBType::STRING)]
    protected ?string $buildingName = null;

    #[Assert\NotBlank(groups: ['kyc'])]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $street = null;

    #[Assert\NotBlank(groups: ['kyc'])]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $town = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $state = null;

    #[Assert\NotBlank(groups: ['kyc'])]
    #[Assert\Regex('/^\d{5}$/', message: 'address.postCode', groups: ['dialog'])]
    #[MongoDB\Field(name: 'postcode', type: MongoDBType::STRING)]
    protected ?string $postCode = null;

    #[Assert\NotBlank(groups: ['kyc'])]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $country = null;

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function setBuildingNumber(?string $buildingNumber): void
    {
        $this->buildingNumber = $buildingNumber;
    }

    public function getBuildingName(): ?string
    {
        return $this->buildingName;
    }

    public function setBuildingName(?string $buildingName): void
    {
        $this->buildingName = $buildingName;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): void
    {
        $this->town = $town;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): void
    {
        $this->postCode = $postCode;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getFlatNumber(): ?string
    {
        return $this->flatNumber;
    }

    public function setFlatNumber(?string $flatNumber): void
    {
        $this->flatNumber = $flatNumber;
    }

    public function isEmpty(): bool
    {
        return null === $this->flatNumber
            && null === $this->buildingName
            && null === $this->buildingNumber
            && null === $this->street
            && null === $this->town
            && null === $this->state
            && null === $this->postCode
            && null === $this->country;
    }

    public function __toString(): string
    {
        return (string) $this->street;
    }

    /**
     * @return array<string, string|null>
     *
     * Tips @see AddressType for dialog specific fields
     */
    public function toArray(string $brand = Theme::PAY): array
    {
        $value = [
            'flatNumber' => $this->flatNumber,
            'buildingNumber' => $this->buildingNumber,
            'buildingName' => $this->buildingName,
            'street' => $this->street,
            'town' => $this->town,
            'state' => $this->state,
            'postCode' => $this->postCode,
            'country' => $this->country,
        ];

        if (Theme::DIALOG === $brand) {
            unset($value['flatNumber']);
        }

        return $value;
    }
}
