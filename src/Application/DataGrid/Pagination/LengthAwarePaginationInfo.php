<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Pagination;

class LengthAwarePaginationInfo extends PaginationInfo
{
    final public const TYPE = 'length_aware';

    public function __construct(
        int $perPage,
        private readonly int $offset,
        private readonly int $filteredCount,
        private readonly int $totalCount
    ) {
        parent::__construct(
            $perPage,
            $this->offset + $perPage < $this->filteredCount,
            $this->offset > 0
        );
    }

    public function getFilteredCount(): ?int
    {
        return $this->filteredCount;
    }

    public function getTotalCount(): ?int
    {
        return $this->totalCount;
    }
}
