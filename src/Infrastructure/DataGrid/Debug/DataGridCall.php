<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Debug;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\Filters\Filters;
use App\Application\DataGrid\Filters\GridRequest;

class DataGridCall
{
    /** @var GridRequest<Filters>|null */
    public ?GridRequest $gridRequest = null;

    /**
     * @param DataGrid<Filters> $dataGrid
     */
    public function __construct(public DataGrid $dataGrid)
    {
    }
}
