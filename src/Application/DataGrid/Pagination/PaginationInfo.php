<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Pagination;

class PaginationInfo
{
    public const TYPE = 'simple';

    public function __construct(
        protected readonly int $perPage,
        protected readonly bool $hasNextPage = false,
        protected readonly bool $hasPreviousPage = false,
    ) {
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function hasNextPage(): bool
    {
        return $this->hasNextPage;
    }

    public function hasPreviousPage(): bool
    {
        return $this->hasPreviousPage;
    }

    public function getType(): string
    {
        return static::TYPE;
    }
}
