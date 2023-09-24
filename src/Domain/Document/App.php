<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Infrastructure\Repository\AppRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'apps', repositoryClass: AppRepository::class)]
class App extends BaseDocument implements CompanyAware
{
    #[MongoDB\ReferenceOne(name: 'companyId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected Company $company;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $name;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $domain;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $appId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $apiKey;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $currency = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $resellerId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $appModel = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $appType = null;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $resellerMetadata = [];

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $default;

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getResellerId(): ?string
    {
        return $this->resellerId;
    }

    public function setResellerId(?string $resellerId): void
    {
        $this->resellerId = $resellerId;
    }

    public function getAppModel(): ?string
    {
        return $this->appModel;
    }

    public function setAppModel(?string $appModel): void
    {
        $this->appModel = $appModel;
    }

    public function getAppType(): ?string
    {
        return $this->appType;
    }

    public function setAppType(?string $appType): void
    {
        $this->appType = $appType;
    }

    /**
     * @return string[]
     */
    public function getResellerMetadata(): array
    {
        return $this->resellerMetadata;
    }

    /**
     * @param string[] $resellerMetadata
     */
    public function setResellerMetadata(array $resellerMetadata): void
    {
        $this->resellerMetadata = $resellerMetadata;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }
}
