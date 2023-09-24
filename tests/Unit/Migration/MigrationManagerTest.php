<?php

declare(strict_types=1);

namespace App\Tests\Unit\Migration;

use App\Domain\Document\Migration;
use App\Infrastructure\Repository\MigrationRepository;
use App\Migration\Migration\AbstractMigration;
use App\Migration\MigrationManager;
use App\Tests\Unit\UnitTestCase;
use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;

class MigrationManagerTest extends UnitTestCase
{
    private MigrationRepository&MockObject $migrationRepository;

    private AbstractMigration&MockObject $migration;

    private MigrationManager $migrationManager;

    private string $newMigrationName = '20221212105052';

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrationRepository = $this->createMock(MigrationRepository::class);
        $this->migration = $this->createMock(AbstractMigration::class);

        $this->migrationManager = new MigrationManager(new ArrayIterator([$this->newMigrationName => $this->migration]), $this->migrationRepository);
    }

    public function testItChecksIfNewMigrationIsAvailable(): void
    {
        $this->migrationRepository->expects(self::once())->method('findAll');

        self::assertTrue($this->migrationManager->isNewMigrationAvailable());
    }

    public function testItExecutesAvailableMigrations(): void
    {
        $this->migration->expects(self::once())->method('up');

        $this->migrationRepository->expects(self::once())->method('save')->with(new Migration($this->newMigrationName));

        $this->migrationManager->execute();
    }

    public function testItMigrateUp(): void
    {
        $this->migration->expects(self::once())->method('up');

        $this->migrationRepository->expects(self::once())->method('save')->with(new Migration($this->newMigrationName));

        $this->migrationManager->up($this->newMigrationName);
    }

    public function testItMigrateDown(): void
    {
        $dbMigration = new Migration($this->newMigrationName);
        $this->migration->expects(self::once())->method('down');

        $this->migrationRepository->method('findAll')->willReturn([$dbMigration]);
        $this->migrationRepository->method('findOneBy')->with(['name' => $this->newMigrationName])->willReturn($dbMigration);
        $this->migrationRepository->expects(self::once())->method('remove')->with($dbMigration);

        $this->migrationManager->down($this->newMigrationName);
    }
}
