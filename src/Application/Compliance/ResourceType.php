<?php

declare(strict_types=1);

namespace App\Application\Compliance;

enum ResourceType: string
{
    case DOCUMENT = 'document';
    case LIVE_PHOTO = 'livePhoto';
    case LIVE_VIDEO = 'liveVideo';
    case CHECK = 'check';
}
