<?php

declare(strict_types=1);

namespace App\Domain\Document\Customer;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Infrastructure\Repository\Customer\CustomerGroupRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'customer_groups', repositoryClass: CustomerGroupRepository::class, readOnly: true)]
class CustomerGroup extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $name = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $description = null;

    #[MongoDB\ReferenceOne(name: 'companyId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected ?Company $company = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }
}
