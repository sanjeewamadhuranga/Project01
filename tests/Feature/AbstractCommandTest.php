<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends KernelTestCase
{
    /**
     * @return array<string, mixed>
     */
    abstract protected function getExecuteArguments(): array;

    abstract protected function getCommandName(): string;

    /**
     * @group smoke
     */
    public function testItExecutes(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);

        $commandTester->execute($this->getExecuteArguments());

        $commandTester->assertCommandIsSuccessful();
    }
}
