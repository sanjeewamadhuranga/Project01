<?php

declare(strict_types=1);

namespace App\Domain\Document\Interfaces;

interface SoftDeleteable
{
    public function isDeleted(): bool;
}
