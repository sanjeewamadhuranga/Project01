<?php

declare(strict_types=1);

namespace App\Domain\Document\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

trait SortableTrait
{
    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $position = 1;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
