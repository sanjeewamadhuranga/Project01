<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Notification;

use App\Domain\Document\Notification\PushNotification;
use App\Infrastructure\Repository\BaseRepository;

/**
 * @extends BaseRepository<PushNotification>
 *
 * @implements NotificationRepository<PushNotification>
 */
class PushNotificationRepository extends BaseRepository implements NotificationRepository
{
    public static function objectClass(): string
    {
        return PushNotification::class;
    }
}
