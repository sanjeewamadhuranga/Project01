<?php

declare(strict_types=1);

namespace App\Application\Security;

interface TokenGenerator
{
    public function generateToken(): string;
}
