<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Filters;

/**
 * Universal value object storing the filters, pagination and sorting information for {@see DataGrid}.
 *
 * @template TFilters of Filters
 */
class GridRequest
{
    /**
     * @param TFilters|null $filters
     */
    public function __construct(
        private readonly ?Filters $filters,
        private readonly Sorting $sorting,
        private readonly Pagination $pagination = new Pagination()
    ) {
    }

    /**
     * @return TFilters|null
     */
    public function getFilters(): ?Filters
    {
        return $this->filters;
    }

    public function getSorting(): Sorting
    {
        return $this->sorting;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function getOffset(): int
    {
        return $this->getPagination()->getOffset();
    }

    public function getLimit(): int
    {
        return $this->getPagination()->getLimit();
    }
}
