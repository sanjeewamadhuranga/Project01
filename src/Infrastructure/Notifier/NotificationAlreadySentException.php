<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Document\Notification\Notification;

class NotificationAlreadySentException extends NotificationException
{
    public function __construct(Notification $notification)
    {
        parent::__construct('Notification has already been marked as sent.', $notification);
    }
}
