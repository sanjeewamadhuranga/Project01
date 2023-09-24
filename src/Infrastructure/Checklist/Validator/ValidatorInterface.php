<?php

declare(strict_types=1);

namespace App\Infrastructure\Checklist\Validator;

use App\Infrastructure\Checklist\CompanyAwareValidationContext;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.constraint_validator')]
interface ValidatorInterface
{
    public function isValid(CompanyAwareValidationContext $context): bool;
}
