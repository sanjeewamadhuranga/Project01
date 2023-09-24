<?php

declare(strict_types=1);

namespace App\Application\Listener\Security;

use App\Domain\Document\Security\Administrator;
use App\Domain\Event\User\AccountSuspendedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class InvalidSignInAttemptListener
{
    private const MAX_INVALID_SIGN_IN_ATTEMPTS = 5;

    public function __construct(private readonly EventDispatcherInterface $eventDispatcher, private readonly ManagerRegistry $managerRegistry)
    {
    }

    #[AsEventListener]
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof Administrator) {
            return;
        }

        $user->clearInvalidSignInAttempts();

        $this->managerRegistry->getManager()->flush();
    }

    #[AsEventListener]
    public function onAuthenticationFailure(LoginFailureEvent $event): void
    {
        $userBadge = $event->getPassport()?->getBadge(UserBadge::class);
        if (!$userBadge instanceof UserBadge) {
            return;
        }

        $user = $userBadge->getUser();
        if (!$user instanceof Administrator) {
            return;
        }

        $user->increaseInvalidSignInAttempts();
        if (!$user->isSuspended() && self::MAX_INVALID_SIGN_IN_ATTEMPTS <= $user->getInvalidSignInAttempts()) {
            $user->suspendAccount();
            $this->eventDispatcher->dispatch(new AccountSuspendedEvent($user));
        }

        $this->managerRegistry->getManager()->flush();
    }
}
