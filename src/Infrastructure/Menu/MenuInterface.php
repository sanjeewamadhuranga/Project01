<?php

declare(strict_types=1);

namespace App\Infrastructure\Menu;

interface MenuInterface
{
    /**
     * @return iterable<MenuItem>
     */
    public function getItems(): iterable;
}
