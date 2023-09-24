<?php

declare(strict_types=1);

namespace App\Migration;

use App\Domain\Document\Migration;
use App\Infrastructure\Repository\MigrationRepository;
use App\Migration\DTO\MigrationDto;
use App\Migration\Exception\MigrationNotFoundException;
use App\Migration\Migration\AbstractMigration;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Traversable;

class MigrationManager
{
    /**
     * @var array<string, MigrationDto>
     */
    private ?array $migrations = null;

    /**
     * @param Traversable<AbstractMigration> $fileMigrations
     */
    public function __construct(
        #[TaggedIterator('app.migration', defaultIndexMethod: 'getKey')] private readonly Traversable $fileMigrations,
        private readonly MigrationRepository $migrationRepository
    ) {
    }

    /**
     * @return array<string, MigrationDto>
     */
    public function getList(): array
    {
        if (is_null($this->migrations)) {
            $this->fetchMigrations();
        }

        return $this->migrations ?? [];
    }

    public function refreshItems(): void
    {
        $this->migrations = null;
    }

    public function up(string $name): void
    {
        $migration = $this->getMigration($name);
        if ($migration->executed || !$migration->available) {
            throw new InvalidArgumentException();
        }

        $this->executeUp($migration->name);
    }

    public function down(string $name): void
    {
        $migration = $this->getMigration($name);
        if (!$migration->executed || !$migration->available) {
            throw new InvalidArgumentException();
        }

        $this->executeDown($migration->name);
    }

    public function execute(): void
    {
        if (!$this->isNewMigrationAvailable()) {
            return;
        }

        foreach ($this->getList() as $migration) {
            if (!$migration->executed && $migration->available) {
                $this->executeUp($migration->name);
            }
        }
    }

    public function isNewMigrationAvailable(): bool
    {
        $migrations = $this->getList();
        foreach ($migrations as $migration) {
            if (!$migration->executed) {
                return true;
            }
        }

        return false;
    }

    public function getLatestExecutedMigrationName(): string
    {
        /** @var ?Migration $migration */
        $migration = $this->migrationRepository->findLatest();

        if (is_null($migration)) {
            throw new MigrationNotFoundException('Latest');
        }

        return $migration->getName();
    }

    private function fetchMigrations(): void
    {
        $this->migrations = [];

        foreach ($this->fileMigrations as $migrationName => $migration) {
            $migrationDto = new MigrationDto((string) $migrationName, $migration->getDescription());
            $this->migrations[(string) $migrationName] = $migrationDto;
        }

        $dbMigrations = $this->migrationRepository->findAll();
        foreach ($dbMigrations as $migration) {
            if (array_key_exists($migration->getName(), $this->migrations)) {
                $this->migrations[$migration->getName()]->executed = true;
                continue;
            }
            $migrationDto = MigrationDto::fromDb($migration);
            $migrationDto->available = false;
            $this->migrations[$migration->getName()] = $migrationDto;
        }
    }

    private function executeUp(string $migrationName): void
    {
        $this->getFileMigration($migrationName)->up();
        $this->migrationRepository->save(new Migration($migrationName));
    }

    private function executeDown(string $migrationName): void
    {
        $this->getFileMigration($migrationName)->down();

        $migration = $this->migrationRepository->findOneBy(['name' => $migrationName]);
        if (!is_null($migration)) {
            $this->migrationRepository->remove($migration);
        }
    }

    private function getFileMigration(string $migrationName): AbstractMigration
    {
        if (!array_key_exists($migrationName, iterator_to_array($this->fileMigrations))) {
            throw new MigrationNotFoundException($migrationName);
        }

        return iterator_to_array($this->fileMigrations)[$migrationName];
    }

    private function getMigration(string $migrationName): MigrationDto
    {
        $migrations = $this->getList();

        if (!array_key_exists($migrationName, $migrations)) {
            throw new MigrationNotFoundException($migrationName);
        }

        return $migrations[$migrationName];
    }
}
