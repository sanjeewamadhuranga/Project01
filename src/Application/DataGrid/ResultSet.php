<?php

declare(strict_types=1);

namespace App\Application\DataGrid;

use App\Application\DataGrid\Pagination\PaginationInfo;
use IteratorAggregate;
use Traversable;

/**
 * {@see DataGrid} result.
 * It stores items returned by the data source and provides the information needed to build pagination.
 *
 * @implements IteratorAggregate<int, mixed>
 */
class ResultSet implements IteratorAggregate
{
    /**
     * @param iterable<mixed> $items
     */
    public function __construct(
        private readonly iterable $items,
        private readonly PaginationInfo $paginationInfo
    ) {
    }

    /**
     * @return iterable<mixed>
     */
    public function getItems(): iterable
    {
        return $this->items;
    }

    public function getPaginationInfo(): PaginationInfo
    {
        return $this->paginationInfo;
    }

    /**
     * @return Traversable<mixed>
     */
    public function getIterator(): Traversable
    {
        return yield from $this->items;
    }
}
