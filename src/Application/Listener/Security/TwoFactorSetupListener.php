<?php

declare(strict_types=1);

namespace App\Application\Listener\Security;

use App\Domain\ActivityLog\ActivityLogType;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use App\Domain\Event\User\TwoFactorDisableEvent;
use App\Domain\Event\User\TwoFactorSetupEvent;
use App\Infrastructure\Repository\LogRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TwoFactorSetupListener
{
    public function __construct(private readonly LogRepository $logRepository)
    {
    }

    #[AsEventListener]
    public function onTwoFactorSetup(TwoFactorSetupEvent $event): void
    {
        $type = match ($event->getType()) {
            Administrator::MFA_SMS => ActivityLogType::TWO_FACTOR_SETUP_SMS,
            default => ActivityLogType::TWO_FACTOR_SETUP_APP,
        };

        $this->logRepository->save(new Log($type, $event->getUser()));
    }

    #[AsEventListener]
    public function onTwoFactorDisable(TwoFactorDisableEvent $event): void
    {
        $type = match ($event->getType()) {
            Administrator::MFA_SMS => ActivityLogType::TWO_FACTOR_DISABLE_SMS,
            default => ActivityLogType::TWO_FACTOR_DISABLE_APP,
        };

        $this->logRepository->save(new Log($type, $event->getUser()));
    }
}
