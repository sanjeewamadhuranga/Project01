<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Filters;

class Sorting
{
    public function __construct(private readonly ?string $key, private readonly SortDirection $direction)
    {
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getDirection(): SortDirection
    {
        return $this->direction;
    }
}
