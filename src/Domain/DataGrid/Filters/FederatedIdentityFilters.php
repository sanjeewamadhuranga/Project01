<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Application\DataGrid\Filters\Filters;

class FederatedIdentityFilters implements Filters
{
    public ?string $email = null;

    public ?string $mobile = null;

    public ?string $sub = null;

    public ?string $name = null;

    public ?string $status = null;
}
