<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Notifications;

use App\Domain\Document\Notification\PushNotification;
use App\Infrastructure\Repository\Notification\PushNotificationRepository;

/**
 * @extends NotificationList<PushNotification>
 */
class PushNotificationList extends NotificationList
{
    public function __construct(PushNotificationRepository $repository)
    {
        parent::__construct($repository);
    }
}
