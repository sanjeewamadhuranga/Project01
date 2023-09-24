<?php

declare(strict_types=1);

namespace App\Tests\Feature\Migration\Command;

use App\Tests\Feature\AbstractCommandTest;

class MigrationCommandTest extends AbstractCommandTest
{
    /**
     * @return array<string, mixed>
     */
    protected function getExecuteArguments(): array
    {
        return [];
    }

    protected function getCommandName(): string
    {
        return 'app:migrations:migrate';
    }
}
