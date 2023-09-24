<?php

declare(strict_types=1);

namespace App\Application\Listener\Security;

use App\Domain\ActivityLog\ActivityLogType;
use App\Domain\Document\Log\Details;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\LogRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvent;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(TwoFactorAuthenticationEvents::FAILURE)]
class TwoFactorAuthenticationFailureListener
{
    public function __construct(private readonly LogRepository $logRepository)
    {
    }

    public function __invoke(TwoFactorAuthenticationEvent $event): void
    {
        /** @var Administrator $user */
        $user = $event->getToken()->getUser();

        $log = new Log(ActivityLogType::AUTHENTICATION_2FA_FAILURE);
        $log->setDetails(Details::fromRequest($event->getRequest(), $user->getUsername()));

        $this->logRepository->save($log);
    }
}
