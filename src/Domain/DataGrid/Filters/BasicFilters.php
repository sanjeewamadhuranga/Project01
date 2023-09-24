<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Application\DataGrid\Filters\Filters;

class BasicFilters implements Filters
{
    private ?string $search = null;

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): void
    {
        $this->search = $search;
    }
}
