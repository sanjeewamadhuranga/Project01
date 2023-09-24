<?php

declare(strict_types=1);

namespace App\Domain\Event\User;

use App\Domain\Document\Security\Administrator;
use Symfony\Contracts\EventDispatcher\Event;

final class AccountSuspendedEvent extends Event
{
    public function __construct(private readonly Administrator $user)
    {
    }

    public function getUser(): Administrator
    {
        return $this->user;
    }
}
