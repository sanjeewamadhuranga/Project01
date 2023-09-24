<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Security\TokenGenerator as TokenGeneratorInterface;

class TokenGenerator implements TokenGeneratorInterface
{
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
