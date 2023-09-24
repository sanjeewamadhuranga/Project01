<?php

declare(strict_types=1);

namespace App\Application\DataGrid;

/**
 * @template TFilters of \App\Application\DataGrid\Filters\Filters
 * @template TItem
 *
 * @extends DataGrid<TFilters>
 */
interface TransformableGrid extends DataGrid
{
    /**
     * @param TItem $item
     */
    public function transform(mixed $item, int|string $index): mixed;
}
