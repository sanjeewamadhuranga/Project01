<?php

declare(strict_types=1);

namespace App\Domain\Restriction;

use App\Domain\Enum\Readable;

enum FieldType: string
{
    use Readable;

    case BIN8 = 'bin8';
    case CARD_TYPE = 'cardType';
    case CURRENCY = 'currency';

    /**
     * @return array<string, string>
     */
    public static function readables(): array
    {
        return [
            self::BIN8->value => 'config_rules.restriction.bin8',
            self::CARD_TYPE->value => 'config_rules.restriction.card_type',
            self::CURRENCY->value => 'config_rules.restriction.currency',
        ];
    }
}
