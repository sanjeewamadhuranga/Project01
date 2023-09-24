<?php

declare(strict_types=1);

namespace App\Domain\Document\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

trait Blacklistable
{
    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $blacklisted = false;

    public function isBlacklisted(): bool
    {
        return $this->blacklisted;
    }

    public function setBlacklisted(bool $blacklisted): void
    {
        $this->blacklisted = $blacklisted;
    }
}
