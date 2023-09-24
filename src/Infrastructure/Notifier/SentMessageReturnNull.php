<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Document\Notification\Notification;

class SentMessageReturnNull extends NotificationException
{
    public function __construct(Notification $notification)
    {
        parent::__construct('Sent Message is returning null from push notification', $notification);
    }
}
