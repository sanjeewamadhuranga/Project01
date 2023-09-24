<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

class MerchantActivityLogFilters extends BasicFilters
{
    public ?string $companyId = null;

    public ?string $userSub = null;

    public ?string $type = null;
}
