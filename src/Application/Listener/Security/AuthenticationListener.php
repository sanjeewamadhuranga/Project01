<?php

declare(strict_types=1);

namespace App\Application\Listener\Security;

use App\Domain\ActivityLog\ActivityLogType;
use App\Domain\Document\Log\Details;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\LogRepository;
use DateTime;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class AuthenticationListener
{
    public function __construct(private readonly RequestStack $requestStack, private readonly LogRepository $logRepository)
    {
    }

    #[AsEventListener]
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $log = new Log(ActivityLogType::AUTHENTICATION_SUCCESS);
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof Administrator) {
            return;
        }

        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $user->setLastLogin(new DateTime());
        $log->setDetails(Details::fromRequest($request, $event->getAuthenticationToken()->getUserIdentifier()));

        $log->setUser($user);
        $log->setObject($user);

        $this->logRepository->save($log);
    }

    #[AsEventListener]
    public function onAuthenticationFailure(LoginFailureEvent $event): void
    {
        $log = new Log(ActivityLogType::AUTHENTICATION_FAILURE);
        $userBadge = $event->getPassport()?->getBadge(UserBadge::class);
        $username = '';
        if ($userBadge instanceof UserBadge) {
            $username = $userBadge->getUserIdentifier();
            try {
                $user = $userBadge->getUser();
                if ($user instanceof Administrator) {
                    $log->setUser($user);
                }
                $log->setObject($user);
            } catch (AuthenticationException) {
                // Ignore the exception - user not found
            }
        }
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $log->setDetails(Details::fromRequest($request, $username));

        $this->logRepository->save($log);
    }
}
