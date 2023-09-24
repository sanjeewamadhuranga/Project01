<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Domain\Tasks\MerchantRequestState;

class MerchantRequestFilters extends BasicFilters
{
    /** @var string[] */
    public array $merchants = [];

    public ?string $requestType = null;

    /** @var string[] */
    public array $assignee = [];

    public ?DateRange $created = null;

    /** @var string[] */
    public array $provider = [];

    public ?MerchantRequestState $state = null;
}
