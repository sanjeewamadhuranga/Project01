<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

class DynamicCodeFilters extends BasicFilters
{
    private ?string $batchDynamicCodeId = null;

    public function getBatchDynamicCodeId(): ?string
    {
        return $this->batchDynamicCodeId;
    }

    public function setBatchDynamicCodeId(?string $batchDynamicCodeId): void
    {
        $this->batchDynamicCodeId = $batchDynamicCodeId;
    }
}
