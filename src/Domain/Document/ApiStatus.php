<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\ApiStatus\MessageType;
use App\Domain\ApiStatus\Platform;
use App\Domain\Document\Interfaces\Activeable;
use App\Domain\Document\Traits\HasActive;
use App\Infrastructure\Repository\ApiStatusRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'api_statuses', repositoryClass: ApiStatusRepository::class)]
class ApiStatus extends BaseDocument implements Activeable
{
    use HasActive;

    #[MongoDB\Field(enumType: MessageType::class)]
    protected ?MessageType $status = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $action = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $title = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $message = null;

    #[MongoDB\Field(enumType: Platform::class)]
    protected ?Platform $platform = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $apiVersion = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $appVersion = null;

    public function getStatus(): ?MessageType
    {
        return $this->status;
    }

    public function setStatus(?MessageType $status): void
    {
        $this->status = $status;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setPlatform(?Platform $platform): void
    {
        $this->platform = $platform;
    }

    public function getApiVersion(): ?string
    {
        return $this->apiVersion;
    }

    public function setApiVersion(?string $apiVersion): void
    {
        $this->apiVersion = $apiVersion;
    }

    public function getAppVersion(): ?string
    {
        return $this->appVersion;
    }

    public function setAppVersion(?string $appVersion): void
    {
        $this->appVersion = $appVersion;
    }
}
