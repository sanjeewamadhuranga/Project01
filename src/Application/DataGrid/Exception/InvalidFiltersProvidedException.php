<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Exception;

use App\Application\DataGrid\Filters\Filters;
use InvalidArgumentException;

class InvalidFiltersProvidedException extends InvalidArgumentException
{
    public function __construct(private readonly string $expectedClass, private readonly Filters $actualFilters)
    {
        parent::__construct(sprintf('Filters should be an instance of %s, %s given', $expectedClass, $actualFilters::class));
    }

    public function getExpectedClass(): string
    {
        return $this->expectedClass;
    }

    public function getActualFilters(): Filters
    {
        return $this->actualFilters;
    }
}
