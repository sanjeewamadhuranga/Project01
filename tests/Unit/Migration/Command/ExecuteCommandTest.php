<?php

declare(strict_types=1);

namespace App\Tests\Unit\Migration\Command;

use App\Migration\Command\ExecuteCommand;
use App\Migration\MigrationManager;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function PHPUnit\Framework\once;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;

class ExecuteCommandTest extends UnitTestCase
{
    private MigrationManager&MockObject $migrationManager;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->migrationManager = $this->createMock(MigrationManager::class);

        $executeCommand = new ExecuteCommand($this->migrationManager);
        $executeCommand->setHelperSet(new HelperSet(['formatter' => new FormatterHelper()]));

        $this->commandTester = new CommandTester($executeCommand);

        parent::setUp();
    }

    public function testItMigrateUpWhenNoOptionProvided(): void
    {
        $migrationName = '20220119031755';

        $this->migrationManager->expects(once())->method('up')->with($migrationName);

        $this->commandTester->execute(['migration' => $migrationName]);
    }

    public function testItMigratesDownWhenDownOptionProvided(): void
    {
        $migrationName = '20220119031750';

        $this->migrationManager->expects(once())->method('down')->with($migrationName);

        $this->commandTester->execute(['migration' => $migrationName, '--down' => true]);
    }
}
