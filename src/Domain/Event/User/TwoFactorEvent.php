<?php

declare(strict_types=1);

namespace App\Domain\Event\User;

use App\Domain\Document\Security\Administrator;
use Symfony\Contracts\EventDispatcher\Event;

abstract class TwoFactorEvent extends Event
{
    public function __construct(private readonly Administrator $user, private readonly string $type)
    {
    }

    public function getUser(): Administrator
    {
        return $this->user;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
