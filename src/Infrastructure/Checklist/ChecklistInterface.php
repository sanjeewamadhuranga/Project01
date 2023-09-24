<?php

declare(strict_types=1);

namespace App\Infrastructure\Checklist;

interface ChecklistInterface
{
    public function validate(CompanyAwareValidationContext $context): CheckResults;
}
