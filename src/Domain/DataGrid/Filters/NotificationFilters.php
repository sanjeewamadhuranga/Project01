<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Domain\Document\Company\Company;

class NotificationFilters extends BasicFilters
{
    private ?Company $company = null;

    private ?string $userId = null;

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }
}
