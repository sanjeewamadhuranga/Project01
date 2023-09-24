<?php

declare(strict_types=1);

namespace App\Domain\Document\Rule;

use App\Domain\Restriction\ComparisonType;
use App\Domain\Restriction\FieldType;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class Restriction
{
    #[MongoDB\Field(enumType: FieldType::class)]
    protected ?FieldType $field = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $value = null;

    #[MongoDB\Field(enumType: ComparisonType::class)]
    protected ?ComparisonType $comparison = null;

    public function getField(): ?FieldType
    {
        return $this->field;
    }

    public function setField(?FieldType $field): void
    {
        $this->field = $field;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getComparison(): ?ComparisonType
    {
        return $this->comparison;
    }

    public function setComparison(?ComparisonType $comparison): void
    {
        $this->comparison = $comparison;
    }
}
