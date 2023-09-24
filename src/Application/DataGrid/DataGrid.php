<?php

declare(strict_types=1);

namespace App\Application\DataGrid;

use App\Application\DataGrid\Filters\GridRequest;

/**
 * Common interface shared by all data grids/lists in the system. It defines the form type to use for filters
 * and responds with a {@see ResultSet} for given {@see GridRequest}.
 *
 * @template TFilters of \App\Application\DataGrid\Filters\Filters
 */
interface DataGrid
{
    /**
     * @param GridRequest<TFilters> $gridRequest
     */
    public function getData(GridRequest $gridRequest): ResultSet;

    /**
     * @return class-string<TFilters>
     */
    public function getFilterDto(): string;
}
