<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Domain\Compliance\DisputeState;

class DisputeFilters extends BasicFilters
{
    /** @var DisputeState[] */
    private array $status = [];

    /**
     * @return DisputeState[]
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param DisputeState[] $status
     */
    public function setStatus(array $status): void
    {
        $this->status = $status;
    }
}
