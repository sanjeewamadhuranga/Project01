<?php

declare(strict_types=1);

namespace App\Application\DTO;

interface Choiceable
{
    public function getId(): ?string;

    public function getChoiceName(): ?string;
}
