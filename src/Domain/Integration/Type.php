<?php

declare(strict_types=1);

namespace App\Domain\Integration;

enum Type: string
{
    case CLICKUP = 'CLICKUP';
    case EMAIL = 'EMAIL';
    case SLACK_NATIVE_WEBHOOK = 'SLACK';
    case JSON_GENERIC_WEBHOOK = 'JSON_GENERIC_WEBHOOK';
}
