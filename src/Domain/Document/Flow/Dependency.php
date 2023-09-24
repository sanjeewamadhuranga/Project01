<?php

declare(strict_types=1);

namespace App\Domain\Document\Flow;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class Dependency
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $field;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $comparison = 'IN';

    #[MongoDB\Field(type: MongoDBType::RAW)]
    protected mixed $value;

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function getComparison(): string
    {
        return $this->comparison;
    }

    public function setComparison(string $comparison): void
    {
        $this->comparison = $comparison;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}
