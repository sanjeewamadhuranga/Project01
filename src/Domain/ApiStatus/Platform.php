<?php

declare(strict_types=1);

namespace App\Domain\ApiStatus;

use App\Domain\Enum\Readable;

enum Platform: string
{
    use Readable;

    case PLATFORM_IOS = 'ios';
    case PLATFORM_ANDROID = 'android';
    case PLATFORM_WEB = 'web';

    /**
     * @return array<string, string>
     */
    public static function readables(): array
    {
        return [
            self::PLATFORM_IOS->value => 'config_status.platform.ios',
            self::PLATFORM_ANDROID->value => 'config_status.platform.android',
            self::PLATFORM_WEB->value => 'config_status.platform.web',
        ];
    }
}
