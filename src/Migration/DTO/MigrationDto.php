<?php

declare(strict_types=1);

namespace App\Migration\DTO;

use App\Domain\Document\Migration;

class MigrationDto
{
    public bool $available = true;

    public bool $executed = false;

    public function __construct(public readonly string $name, public readonly ?string $description = null)
    {
    }

    public static function fromDb(Migration $migration): self
    {
        $dto = new self($migration->getName());
        $dto->executed = true;

        return $dto;
    }
}
