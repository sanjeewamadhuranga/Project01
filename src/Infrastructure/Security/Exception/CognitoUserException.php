<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Exception;

use RuntimeException;

class CognitoUserException extends RuntimeException
{
    /**
     * @param mixed[] $info
     */
    public function __construct(string $message, private readonly array $info)
    {
        parent::__construct($message);
    }

    /**
     * @return mixed[]
     */
    public function getInfo(): array
    {
        return $this->info;
    }
}
