<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Filters;

final class Pagination
{
    final public const DEFAULT_LIMIT = 25;

    public function __construct(
        private readonly int $offset = 0,
        private readonly int $limit = self::DEFAULT_LIMIT,
        private readonly ?string $cursor = null
    ) {
    }

    /**
     * @return int<0, max>
     */
    public function getOffset(): int
    {
        return max(0, $this->offset);
    }

    /**
     * @return int<1, max>
     */
    public function getLimit(): int
    {
        return max($this->limit, 1);
    }

    public function hasLimit(): bool
    {
        return $this->limit > 0;
    }

    public function getCursor(): ?string
    {
        return $this->cursor;
    }
}
