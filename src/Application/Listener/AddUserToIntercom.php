<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Company\IntercomInterface;
use App\Domain\Event\Company\UserCreated;
use App\Domain\Settings\Features;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class AddUserToIntercom
{
    public function __construct(private readonly IntercomInterface $intercom, private readonly Features $features)
    {
    }

    public function __invoke(UserCreated $event): void
    {
        if ($this->features->isIntercomEnabled()) {
            $this->intercom->syncUser($event->getCompany(), $event->getUser());
        }
    }
}
