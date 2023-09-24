<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Notification;

use App\Domain\Document\Notification\CustomEmail;
use App\Infrastructure\Repository\BaseRepository;

/**
 * @extends BaseRepository<CustomEmail>
 *
 * @implements NotificationRepository<CustomEmail>
 */
class CustomEmailRepository extends BaseRepository implements NotificationRepository
{
    public static function objectClass(): string
    {
        return CustomEmail::class;
    }
}
