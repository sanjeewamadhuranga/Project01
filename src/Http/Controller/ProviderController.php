<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Infrastructure\Form\Onboarding\BulkProviderOnboardingType;
use App\Infrastructure\ProviderOnboarding\BulkCheckDto;
use App\Infrastructure\ProviderOnboarding\BulkProviderOnboardingDto;
use App\Infrastructure\ProviderOnboarding\ChecklistFinder;
use App\Infrastructure\ProviderOnboarding\ProviderAwareValidationContext;
use App\Infrastructure\Repository\Company\CompanyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/provider', name: 'provider_')]
class ProviderController extends BaseController
{
    #[Route('/bulk', name: 'bulk')]
    #[IsGranted(Permission::MERCHANT_PAYMENT_METHOD.Action::ENABLE)]
    public function bulk(
        Request $request,
        CompanyRepository $companyRepository,
        ChecklistFinder $checklistFinder,
    ): Response {
        $dto = new BulkProviderOnboardingDto();
        $form = $this->createForm(BulkProviderOnboardingType::class, $dto)->handleRequest($request);
        /** @var BulkCheckDto[] $bulkChecks */
        $bulkChecks = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $companies = $companyRepository->getAllForBulkCheck($dto->currency, $dto->provider);
            foreach ($companies as $company) {
                $bulkChecks[] = new BulkCheckDto(
                    $company,
                    $checklistFinder->find($dto->provider->getValue())->validate(new ProviderAwareValidationContext($company, $dto->provider))
                );
            }
        }

        return $this->renderForm(
            'provider/checks.html.twig',
            [
                'bulkChecks' => $bulkChecks,
                'form' => $form,
                'backUrl' => $this->generateUrl('configuration_providers_index'),
                'title' => 'provider_onboarding.bulk.title',
                'subTitle' => 'provider_onboarding.bulk.title',
            ]
        );
    }
}
