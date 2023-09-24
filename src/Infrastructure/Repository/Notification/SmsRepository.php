<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Notification;

use App\Domain\Document\Notification\Sms;
use App\Infrastructure\Repository\BaseRepository;

/**
 * @extends BaseRepository<Sms>
 *
 * @implements NotificationRepository<Sms>
 */
class SmsRepository extends BaseRepository implements NotificationRepository
{
    public static function objectClass(): string
    {
        return Sms::class;
    }
}
