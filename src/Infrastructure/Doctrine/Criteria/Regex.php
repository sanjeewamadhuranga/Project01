<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Criteria;

use MongoDB\BSON\Regex as MongoRegex;

/**
 * Handles regex quoting and sanitization for commonly used "contains" filter.
 */
final class Regex
{
    final public const DEFAULT_FLAGS = 'i';

    public static function contains(string $value, string $flags = self::DEFAULT_FLAGS): MongoRegex
    {
        return new MongoRegex(preg_quote($value, null), $flags);
    }

    public static function startsWith(string $value, string $flags = self::DEFAULT_FLAGS): MongoRegex
    {
        return new MongoRegex('^'.preg_quote($value, null), $flags);
    }

    public static function endsWith(string $value, string $flags = self::DEFAULT_FLAGS): MongoRegex
    {
        return new MongoRegex(preg_quote($value, null).'$', $flags);
    }

    public static function equals(string $value, string $flags = self::DEFAULT_FLAGS): MongoRegex
    {
        return new MongoRegex('^'.preg_quote($value, null).'$', $flags);
    }
}
