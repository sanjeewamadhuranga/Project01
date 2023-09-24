<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Notifications;

use App\Domain\Document\Notification\Sms;
use App\Infrastructure\Repository\Notification\SmsRepository;

/**
 * @extends NotificationList<Sms>
 */
class SmsList extends NotificationList
{
    public function __construct(SmsRepository $repository)
    {
        parent::__construct($repository);
    }
}
