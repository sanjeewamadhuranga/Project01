<?php

declare(strict_types=1);

namespace App\Tests\Unit\Migration\Command;

use App\Migration\Command\GenerateCommand;
use App\Migration\MigrationGenerator;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function PHPUnit\Framework\once;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateCommandTest extends UnitTestCase
{
    private MigrationGenerator&MockObject $migrationGenerator;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->migrationGenerator = $this->createMock(MigrationGenerator::class);

        $generateCommand = new GenerateCommand($this->migrationGenerator);
        $generateCommand->setHelperSet(new HelperSet(['formatter' => new FormatterHelper()]));

        $this->commandTester = new CommandTester($generateCommand);

        parent::setUp();
    }

    public function testItUsesMigrationGeneratorToGenerateNewMigration(): void
    {
        $this->migrationGenerator->expects(once())->method('generateMigrationFile');

        $this->commandTester->execute([]);
    }

    public function testItWritesInformationAboutGeneratedMigration(): void
    {
        $migrationName = '20220119031153';
        $this->migrationGenerator->method('generateMigrationFile')->willReturn($migrationName);

        $this->commandTester->execute([]);

        self::assertStringContainsString(sprintf('%s has been generated.', $migrationName), $this->commandTester->getDisplay());
    }
}
