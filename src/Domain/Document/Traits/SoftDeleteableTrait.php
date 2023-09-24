<?php

declare(strict_types=1);

namespace App\Domain\Document\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

trait SoftDeleteableTrait
{
    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $deleted = false;

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }
}
