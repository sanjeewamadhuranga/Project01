<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\Command;

use App\Tests\Feature\AbstractCommandTest;

class CreateAdministratorCommandTest extends AbstractCommandTest
{
    /**
     * @return array<string, mixed>
     */
    protected function getExecuteArguments(): array
    {
        return [
            'email' => uniqid('email', true).'@pay.com',
            'password' => 'somePassword',
            'roles' => [1, 2],
        ];
    }

    protected function getCommandName(): string
    {
        return 'app:administrator:create';
    }
}
