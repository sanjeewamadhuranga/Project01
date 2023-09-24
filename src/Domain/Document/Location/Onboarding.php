<?php

declare(strict_types=1);

namespace App\Domain\Document\Location;

use App\Domain\Document\BaseDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class Onboarding extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $provider;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $request;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $response;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $result;

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function setRequest(string $request): void
    {
        $this->request = $request;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function setResponse(string $response): void
    {
        $this->response = $response;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }
}
