<?php

declare(strict_types=1);

namespace App\Infrastructure\Checklist;

use App\Infrastructure\ProviderOnboarding\Constraint\CompanyCurrency;
use App\Infrastructure\ProviderOnboarding\Constraint\HasFixedMdrInSubscriptionPlan;
use App\Infrastructure\ProviderOnboarding\Constraint\ProviderCurrencies;
use App\Infrastructure\ProviderOnboarding\Constraint\ProviderEnabled;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractChecklist implements ChecklistInterface
{
    private ContainerInterface $locator;

    #[Required]
    public function setLocator(#[TaggedLocator('app.constraint_validator')] ContainerInterface $locator): void
    {
        $this->locator = $locator;
    }

    public function validate(CompanyAwareValidationContext $context): CheckResults
    {
        $results = [];

        foreach ($this->getConstraints() as $constraint) {
            $validator = $this->locator->get($constraint->getValidatorClass());
            $results[] = $constraint->handle($validator, $context);
        }

        return new CheckResults($results);
    }

    /**
     * @return iterable<AbstractConstraint>
     */
    public function getConstraints(): iterable
    {
        yield new ProviderEnabled();
        yield new ProviderCurrencies();
        yield new HasFixedMdrInSubscriptionPlan();
        yield new CompanyCurrency();
    }
}
