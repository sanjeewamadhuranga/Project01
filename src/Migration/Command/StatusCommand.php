<?php

declare(strict_types=1);

namespace App\Migration\Command;

use App\Migration\DTO\MigrationDto;
use App\Migration\MigrationManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:migrations:status')]
class StatusCommand extends Command
{
    public function __construct(private readonly MigrationManager $migrationManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Displays information about migrations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');
        $io = new SymfonyStyle($input, $output);
        $output->getFormatter()->setStyle('danger', new OutputFormatterStyle('red', null, ['bold']));
        $output->getFormatter()->setStyle('new_migration', new OutputFormatterStyle('green', null, ['bold']));
        $output->getFormatter()->setStyle('none', new OutputFormatterStyle());

        $migrations = $this->migrationManager->getList();

        $counter = [
            'executed' => count(array_filter($migrations, static fn (MigrationDto $migration) => $migration->executed)),
            'available' => count(array_filter($migrations, static fn (MigrationDto $migration) => $migration->available)),
            'new' => count(array_filter($migrations, static fn (MigrationDto $migration) => $migration->available && !$migration->executed)),
            'unavailable' => count(array_filter($migrations, static fn (MigrationDto $migration) => !$migration->available && $migration->executed)),
        ];

        $io->table(
            ['Name', 'Executed', 'Available'],
            array_map(
                static function (MigrationDto $migration) use ($formatter) {
                    $executed = $migration->executed ? 'Yes' : 'No';
                    $available = $migration->available ? 'Yes' : 'No';

                    $style = $migration->available ? 'none' : 'danger';
                    if ($migration->available && !$migration->executed) {
                        $style = 'new_migration';
                    }

                    return [
                        $formatter->formatBlock($migration->name, $style),
                        $formatter->formatBlock($executed, $style),
                        $formatter->formatBlock($available, $style),
                    ];
                },
                $migrations
            )
        );

        $migrationsUnavailable = $counter['unavailable'] > 0 ? $formatter->formatBlock((string) $counter['unavailable'], 'danger') : ' 0 ';
        $newMigrations = $counter['new'] > 0 ? $formatter->formatBlock((string) $counter['new'], 'new_migration') : ' 0 ';

        $io->table([], [
            ['Migrations executed:', $formatter->formatBlock((string) $counter['executed'], 'none')],
            ['Migrations unavailable:', $migrationsUnavailable],
            ['Migrations available:', $formatter->formatBlock((string) $counter['available'], 'none')],
            ['Migrations new:', $newMigrations],
        ]);

        return Command::SUCCESS;
    }
}
