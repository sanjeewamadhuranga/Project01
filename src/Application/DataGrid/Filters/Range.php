<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Filters;

/**
 * @template T
 */
interface Range
{
    /**
     * @return T|null
     */
    public function getMin(): mixed;

    /**
     * @return T|null
     */
    public function getMax(): mixed;
}
