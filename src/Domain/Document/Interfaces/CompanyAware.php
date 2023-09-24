<?php

declare(strict_types=1);

namespace App\Domain\Document\Interfaces;

use App\Domain\Document\Company\Company;

interface CompanyAware
{
    public function getCompany(): Company;
}
