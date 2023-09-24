<?php

declare(strict_types=1);

namespace App\Domain\Document\Interfaces;

interface Activeable
{
    public function isActive(): bool;

    public function setActive(bool $active): void;
}
