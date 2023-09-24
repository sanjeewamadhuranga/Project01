<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Notification;

use App\Domain\Repository\Repository;

/**
 * @template TDocument of object
 *
 * @extends Repository<TDocument>
 */
interface NotificationRepository extends Repository
{
}
