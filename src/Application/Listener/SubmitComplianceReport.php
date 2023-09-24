<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Compliance\OnfidoInterface;
use App\Domain\Document\Company\ComplianceReport;
use App\Domain\Event\Company\UserCreated;
use App\Domain\Settings\Config;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class SubmitComplianceReport
{
    public function __construct(private readonly OnfidoInterface $onfido, private readonly Config $config)
    {
    }

    public function __invoke(UserCreated $event): void
    {
        if (!$this->config->getFeatures()->isKycEnabled()) {
            return;
        }

        $user = $event->getUser();

        if (null === $user->getAddresses()) {
            return;
        }

        if (!$user->isRequireKyc()) {
            return;
        }

        $complianceReport = new ComplianceReport();
        $complianceReport->setApplicantId($this->onfido->createApplicant($user));
        $user->setComplianceReport($complianceReport);
    }
}
