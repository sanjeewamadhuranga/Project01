<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Infrastructure\Repository\CategoryRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;

#[MongoDB\Document(collection: 'categories', repositoryClass: CategoryRepository::class)]
class Category extends BaseDocument implements Stringable
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $name;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $companyId;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    public function setCompanyId(string $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
