<?php

declare(strict_types=1);

namespace App\Domain\Document\Customer;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Infrastructure\Repository\Customer\CustomerHistoryRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'customer_history', repositoryClass: CustomerHistoryRepository::class, readOnly: true)]
class CustomerHistory extends BaseDocument
{
    #[MongoDB\ReferenceOne(name: 'customerId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Customer::class)]
    protected ?Customer $customer = null;

    #[MongoDB\ReferenceOne(name: 'companyId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected ?Company $company = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $title = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $description = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $actionType = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $resourceType = null;

    #[MongoDB\Field(type: MongoDBType::OBJECTID)]
    protected ?string $resourceId = null;

    #[MongoDB\EmbedOne(targetDocument: User::class)]
    protected ?User $createdBy = null;

    /**
     * @var array<string, mixed>
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $metadata = [];

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getActionType(): ?string
    {
        return $this->actionType;
    }

    public function setActionType(?string $actionType): void
    {
        $this->actionType = $actionType;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(?string $resourceType): void
    {
        $this->resourceType = $resourceType;
    }

    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function setResourceId(?string $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed[]
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param mixed[] $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
