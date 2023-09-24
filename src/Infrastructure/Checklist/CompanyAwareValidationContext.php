<?php

declare(strict_types=1);

namespace App\Infrastructure\Checklist;

use App\Domain\Document\Company\Company;

class CompanyAwareValidationContext
{
    public function __construct(private readonly Company $company)
    {
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
