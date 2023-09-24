<?php

declare(strict_types=1);

namespace App\Domain\Compliance;

use App\Domain\Enum\Classable;
use App\Domain\Enum\Readable;

enum PayoutBlockStatus: string implements classable
{
    use Readable;

    case OPEN = 'open';
    case IN_REVIEW = 'in_review';
    case IN_APPROVAL = 'in_approval';
    case CLOSED = 'closed';

    /**
     * @return array<string,string>
     */
    public static function classNames(): array
    {
        return [
            self::OPEN->value => 'info',
            self::IN_REVIEW->value => 'warning',
            self::IN_APPROVAL->value => 'success',
            self::CLOSED->value => 'dark',
        ];
    }

    public function className(): string
    {
        return self::classNames()[$this->value] ?? 'primary';
    }

    /**
     * @return array<string, string>
     */
    public static function readables(): array
    {
        return [
            self::OPEN->value => 'compliance.case.status.open',
            self::IN_REVIEW->value => 'compliance.case.status.in_review',
            self::IN_APPROVAL->value => 'compliance.case.status.in_approval',
            self::CLOSED->value => 'compliance.case.status.closed',
        ];
    }
}
