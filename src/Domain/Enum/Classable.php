<?php

declare(strict_types=1);

namespace App\Domain\Enum;

interface Classable
{
    /**
     * @return array<string, string>
     */
    public static function classNames(): array;

    public function className(): string;
}
