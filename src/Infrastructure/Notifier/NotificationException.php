<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Document\Notification\Notification;
use RuntimeException;

abstract class NotificationException extends RuntimeException
{
    public function __construct(string $message, private readonly Notification $notification)
    {
        parent::__construct($message);
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}
