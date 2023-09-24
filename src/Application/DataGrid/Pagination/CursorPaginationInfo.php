<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Pagination;

class CursorPaginationInfo extends PaginationInfo
{
    final public const TYPE = 'cursor';

    public function __construct(
        int $perPage,
        private readonly ?string $currentCursor = null,
        private readonly ?string $nextCursor = null,
        private readonly ?string $previousCursor = null
    ) {
        parent::__construct(
            $perPage,
            null !== $this->nextCursor,
            null !== $this->previousCursor
        );
    }

    public function getNextCursor(): ?string
    {
        return $this->nextCursor;
    }

    public function getPreviousCursor(): ?string
    {
        return $this->previousCursor;
    }

    public function getCurrentCursor(): ?string
    {
        return $this->currentCursor;
    }
}
