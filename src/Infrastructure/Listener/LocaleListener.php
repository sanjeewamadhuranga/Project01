<?php

declare(strict_types=1);

namespace App\Infrastructure\Listener;

use App\Application\Locale\LocaleSetter;
use App\Domain\Document\Security\Administrator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LocaleListener
{
    public function __construct(private readonly LocaleSetter $localeSetter)
    {
    }

    #[AsEventListener(priority: 20)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->localeSetter->setLocale($event->getRequest());
    }

    #[AsEventListener(priority: 20)]
    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof Administrator) {
            return;
        }

        if (null !== $user->getLocale()) {
            $event->getRequest()->getSession()->set(LocaleSetter::LOCALE_ATTRIBUTE, $user->getLocale());
        }
    }
}
