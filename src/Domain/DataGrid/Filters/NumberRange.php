<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Application\DataGrid\Filters\Range;

/**
 * @implements Range<float>
 */
class NumberRange implements Range
{
    private ?float $min = null;

    private ?float $max = null;

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function setMin(?float $min): void
    {
        $this->min = $min;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function setMax(?float $max): void
    {
        $this->max = $max;
    }
}
