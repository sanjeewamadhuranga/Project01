<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

class PayoutBlockFilters extends BasicFilters
{
    private ?string $status = null;

    private bool $onlyMe = false;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function isOnlyMe(): bool
    {
        return $this->onlyMe;
    }

    public function setOnlyMe(bool $onlyMe): void
    {
        $this->onlyMe = $onlyMe;
    }
}
