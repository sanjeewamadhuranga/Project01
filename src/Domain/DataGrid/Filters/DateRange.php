<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Application\DataGrid\Filters\Range;
use DateTime;

/**
 * @implements Range<DateTime>
 */
class DateRange implements Range
{
    private ?DateTime $min = null;

    private ?DateTime $max = null;

    public function getMin(): ?DateTime
    {
        return $this->min;
    }

    public function setMin(?DateTime $min): void
    {
        $this->min = $min;
    }

    public function getMax(): ?DateTime
    {
        return $this->max;
    }

    public function setMax(?DateTime $max): void
    {
        $this->max = $max;
    }
}
