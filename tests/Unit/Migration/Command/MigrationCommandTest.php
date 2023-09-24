<?php

declare(strict_types=1);

namespace App\Tests\Unit\Migration\Command;

use App\Migration\Command\MigrationCommand;
use App\Migration\MigrationManager;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function PHPUnit\Framework\never;
use function PHPUnit\Framework\once;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;

class MigrationCommandTest extends UnitTestCase
{
    private MigrationManager&MockObject $migrationManager;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->migrationManager = $this->createMock(MigrationManager::class);

        $executeCommand = new MigrationCommand($this->migrationManager);
        $executeCommand->setHelperSet(new HelperSet(['formatter' => new FormatterHelper()]));

        $this->commandTester = new CommandTester($executeCommand);

        parent::setUp();
    }

    public function testItUsesExecuteMethodFromMigrationManagerWhenNoAliasProvided(): void
    {
        $this->migrationManager->expects(self::once())->method('execute');

        $this->commandTester->execute([]);
    }

    public function testItRevertsPrevMigrationWhenPrevAliasProvided(): void
    {
        $lastMigration = '20220119031756';
        $this->migrationManager->method('getLatestExecutedMigrationName')->willReturn($lastMigration);

        $this->migrationManager->expects(never())->method('execute');
        $this->migrationManager->expects(once())->method('down')->with($lastMigration);

        $this->commandTester->execute(['alias' => 'prev']);
    }
}
