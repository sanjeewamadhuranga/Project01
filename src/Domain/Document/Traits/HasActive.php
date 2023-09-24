<?php

declare(strict_types=1);

namespace App\Domain\Document\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

trait HasActive
{
    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $active = true;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
