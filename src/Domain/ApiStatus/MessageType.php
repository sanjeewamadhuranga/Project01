<?php

declare(strict_types=1);

namespace App\Domain\ApiStatus;

use App\Domain\Enum\Readable;

enum MessageType: string
{
    use Readable;

    case MESSAGE = 'message';
    case BLOCKING = 'blocking';
    case LOGOUT = 'logout';

    /**
     * @return array<string, string>
     */
    public static function readables(): array
    {
        return [
            self::MESSAGE->value => 'config_status.status.message',
            self::BLOCKING->value => 'config_status.status.blocking',
            self::LOGOUT->value => 'config_status.status.logout',
        ];
    }
}
