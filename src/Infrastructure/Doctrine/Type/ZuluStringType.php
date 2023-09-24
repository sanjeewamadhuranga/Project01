<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use Carbon\Carbon;
use Doctrine\ODM\MongoDB\Types\DateType;

/**
 * Stores the DateTime as Zulu string in the database rather than Mongo Date object for compatibility with v2 code base.
 */
class ZuluStringType extends DateType
{
    public function convertToDatabaseValue($value): ?string
    {
        if (null === $value) {
            return $value;
        }

        return (new Carbon($value))->toIso8601ZuluString();
    }
}
