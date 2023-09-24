<?php

declare(strict_types=1);

namespace App\Domain\Enum;

trait Renderable
{
    public function className(): string
    {
        return static::classNames()[$this->value];
    }

    public function icon(): string
    {
        return static::icons()[$this->value];
    }

    /**
     * @return array<string|int, string>
     */
    abstract public static function icons(): array;

    /**
     * @return array<string|int, string>
     */
    abstract public static function classNames(): array;
}
