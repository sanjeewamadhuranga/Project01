<?php

declare(strict_types=1);

namespace App\Domain\Enum;

trait Readable
{
    public function readable(): string
    {
        return static::readables()[$this->value] ?? $this->value;
    }

    /**
     * @return array<string|int, string>
     */
    abstract public static function readables(): array;
}
