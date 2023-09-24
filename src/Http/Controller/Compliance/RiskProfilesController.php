<?php

declare(strict_types=1);

namespace App\Http\Controller\Compliance;

use App\Domain\Document\Compliance\RiskProfile;
use App\Http\Controller\CrudController;
use App\Infrastructure\Form\Compliance\RiskProfileType;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @extends CrudController<RiskProfile>
 */
#[Route('/compliance/risk-profiles', name: 'compliance_risk_profile_')]
class RiskProfilesController extends CrudController
{
    protected static function getFormType(): string
    {
        return RiskProfileType::class;
    }

    protected static function getItemClass(): string
    {
        return RiskProfile::class;
    }

    public function getIndexComponent(): ?string
    {
        return 'risk-profile-list';
    }

    protected static function getKey(): string
    {
        return 'compliance.risk_profile';
    }
}
