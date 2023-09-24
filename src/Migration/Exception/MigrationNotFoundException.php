<?php

declare(strict_types=1);

namespace App\Migration\Exception;

use Exception;

class MigrationNotFoundException extends Exception
{
    public function __construct(string $migrationName)
    {
        $message = sprintf('%s migration not found.', $migrationName);

        parent::__construct($message);
    }
}
