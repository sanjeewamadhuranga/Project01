<?php

declare(strict_types=1);

namespace App\Application\DTO;

class ChoiceOption
{
    public function __construct(private readonly ?string $value, private readonly ?string $label)
    {
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
