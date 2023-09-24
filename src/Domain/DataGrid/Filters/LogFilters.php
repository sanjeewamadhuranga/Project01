<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

class LogFilters extends BasicFilters
{
    private ?string $objectId = null;

    public function getObjectId(): ?string
    {
        return $this->objectId;
    }

    public function setObjectId(?string $objectId): void
    {
        $this->objectId = $objectId;
    }
}
