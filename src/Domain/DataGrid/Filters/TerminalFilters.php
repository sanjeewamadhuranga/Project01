<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

class TerminalFilters extends BasicFilters
{
    private ?string $companyId = null;

    public function getCompanyId(): ?string
    {
        return $this->companyId;
    }

    public function setCompanyId(?string $companyId): void
    {
        $this->companyId = $companyId;
    }
}
