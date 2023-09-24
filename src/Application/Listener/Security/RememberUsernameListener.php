<?php

declare(strict_types=1);

namespace App\Application\Listener\Security;

use DateTime;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener]
class RememberUsernameListener
{
    public function __invoke(LoginSuccessEvent $event): void
    {
        $request = $event->getRequest()->request;
        $event->getResponse()?->headers->setCookie(
            Cookie::create(
                'last_username',
                null === $request->get('remember') ? '' : (string) $request->get('username'),
                new DateTime('+ 7 days'),
            )
        );
    }
}
