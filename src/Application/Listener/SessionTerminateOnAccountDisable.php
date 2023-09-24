<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Security\UserChecker;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This http request event listener is used to check for the following states of a logged-in user account.
 *
 *  * If the user account has been disabled (isEnabled = false)
 *  * If the user account has been suspended (isSuspended = true)
 *  * If the user account has been expired (isExpired = true)
 *
 * The listener will check for every HTTP request triggered by the users
 * and will force logout from the application upon any of the states occur.
 *
 * This will address a key security concern of not keeping any existing logged-in sessions available for inactive user accounts.
 */
class SessionTerminateOnAccountDisable
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly UserChecker $checker
    ) {
    }

    #[AsEventListener]
    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof Administrator) {
            return;
        }

        $this->checker->checkPreAuth($user);
    }
}
