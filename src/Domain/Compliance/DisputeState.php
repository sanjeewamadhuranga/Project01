<?php

declare(strict_types=1);

namespace App\Domain\Compliance;

use App\Domain\Enum\Classable;

enum DisputeState: string implements Classable
{
    case NEW = 'compliance.dispute_state.new';
    case PROCESSING = 'compliance.dispute_state.processing';
    case CLOSED = 'compliance.dispute_state.closed';

    /**
     * @return array<string,string>
     */
    public static function classNames(): array
    {
        return [
            self::NEW->value => 'dark',
            self::PROCESSING->value => 'info',
            self::CLOSED->value => 'success',
        ];
    }

    public function className(): string
    {
        return self::classNames()[$this->value] ?? 'primary';
    }
}
