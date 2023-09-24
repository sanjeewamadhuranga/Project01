<?php

declare(strict_types=1);

namespace App\Domain\Enum;

interface Iconable
{
    /**
     * @return array<string, string>
     */
    public static function icons(): array;

    public function icon(): string;
}
