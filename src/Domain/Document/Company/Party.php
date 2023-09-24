<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;

#[MongoDB\EmbeddedDocument]
class Party implements Stringable
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $firstName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $lastName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $documentId = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $corporate = false;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $corporateName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $dob = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $email = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $percentage = null;

    #[MongoDB\EmbedOne(targetDocument: Address::class)]
    protected ?Address $address = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $title = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $idType = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $idNumber = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $politicallyExposed = false;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $relationship = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $designation = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $name = null; // Politically exposed name.

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getDocumentId(): ?string
    {
        return $this->documentId;
    }

    public function setDocumentId(?string $documentId): void
    {
        $this->documentId = $documentId;
    }

    public function isCorporate(): bool
    {
        return $this->corporate;
    }

    public function setCorporate(bool $corporate): void
    {
        $this->corporate = $corporate;
    }

    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    public function setCorporateName(?string $corporateName): void
    {
        $this->corporateName = $corporateName;
    }

    public function getDob(): ?string
    {
        return $this->dob;
    }

    public function setDob(?string $dob): void
    {
        $this->dob = $dob;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPercentage(): ?string
    {
        return $this->percentage;
    }

    public function setPercentage(?string $percentage): void
    {
        $this->percentage = $percentage;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): void
    {
        $this->address = $address;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getIdType(): ?string
    {
        return $this->idType;
    }

    public function setIdType(?string $idType): void
    {
        $this->idType = $idType;
    }

    public function getIdNumber(): ?string
    {
        return $this->idNumber;
    }

    public function setIdNumber(?string $idNumber): void
    {
        $this->idNumber = $idNumber;
    }

    public function isPoliticallyExposed(): bool
    {
        return $this->politicallyExposed;
    }

    public function setPoliticallyExposed(bool $politicallyExposed): void
    {
        $this->politicallyExposed = $politicallyExposed;
    }

    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function setRelationship(?string $relationship): void
    {
        $this->relationship = $relationship;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): void
    {
        $this->designation = $designation;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function isComplete(): bool
    {
        if (null === $this->firstName || '' === $this->firstName) {
            return false;
        }

        if (null === $this->corporateName || '' === $this->corporateName) {
            return false;
        }

        return true;
    }
}
