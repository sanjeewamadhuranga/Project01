<?php

declare(strict_types=1);

namespace App\Migration\Command;

use App\Migration\MigrationManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:migrations:execute')]
class ExecuteCommand extends Command
{
    public function __construct(private readonly MigrationManager $migrationManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Executes selected migration up or down.')
            ->addArgument('migration', InputArgument::REQUIRED, 'The name of migrations that should be executed')
            ->addOption('down', null, InputOption::VALUE_NONE, 'Should the migration be executed down?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migration = $input->getArgument('migration');
        $down = (bool) $input->getOption('down');

        $down ? $this->migrationManager->down($migration) : $this->migrationManager->up($migration);

        return Command::SUCCESS;
    }
}
