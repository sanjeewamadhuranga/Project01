<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Document\Notification\Notification;

class UnsupportedNotificationException extends NotificationException
{
    public function __construct(Notification $notification)
    {
        parent::__construct(sprintf('Unsupported notification provided: %s', $notification::class), $notification);
    }
}
