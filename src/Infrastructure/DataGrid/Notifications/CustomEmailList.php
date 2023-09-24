<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Notifications;

use App\Domain\Document\Notification\CustomEmail;
use App\Infrastructure\Repository\Notification\CustomEmailRepository;

/**
 * @extends NotificationList<CustomEmail>
 */
class CustomEmailList extends NotificationList
{
    public function __construct(CustomEmailRepository $repository)
    {
        parent::__construct($repository);
    }
}
