<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Infrastructure\Repository\CardRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;

#[MongoDB\Document(collection: 'cards', repositoryClass: CardRepository::class)]
class Card extends BaseDocument implements Stringable
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $code;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $description;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $bin6;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $bin8;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $brand;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $category;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $mdrCode;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $reseller;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $mpgsRouting;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getBin6(): string
    {
        return $this->bin6;
    }

    public function setBin6(string $bin6): void
    {
        $this->bin6 = $bin6;
    }

    public function getBin8(): string
    {
        return $this->bin8;
    }

    public function setBin8(string $bin8): void
    {
        $this->bin8 = $bin8;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getMdrCode(): string
    {
        return $this->mdrCode;
    }

    public function setMdrCode(string $mdrCode): void
    {
        $this->mdrCode = $mdrCode;
    }

    public function getReseller(): string
    {
        return $this->reseller;
    }

    public function setReseller(string $reseller): void
    {
        $this->reseller = $reseller;
    }

    public function isMpgsRouting(): bool
    {
        return $this->mpgsRouting;
    }

    public function setMpgsRouting(bool $mpgsRouting): void
    {
        $this->mpgsRouting = $mpgsRouting;
    }

    public function __toString()
    {
        return $this->getCode();
    }
}
