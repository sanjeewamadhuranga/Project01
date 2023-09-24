<?php

declare(strict_types=1);

namespace App\Domain\Restriction;

use App\Domain\Enum\Readable;

enum ComparisonType: string
{
    use Readable;

    case EQUALS = 'EQUALS';
    case NOT_EQUAL = 'NOT_EQUAL';
    case IN = 'IN';
    case NOT_IN = 'NOT_IN';

    /**
     * @return array<string, string>
     */
    public static function readables(): array
    {
        return [
            self::EQUALS->value => 'config_rules.comparison.equals',
            self::NOT_EQUAL->value => 'config_rules.comparison.not_equal',
            self::IN->value => 'config_rules.comparison.in',
            self::NOT_IN->value => 'config_rules.comparison.not_in',
        ];
    }
}
