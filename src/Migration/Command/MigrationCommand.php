<?php

declare(strict_types=1);

namespace App\Migration\Command;

use App\Migration\MigrationManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:migrations:migrate')]
class MigrationCommand extends Command
{
    public function __construct(private readonly MigrationManager $migrationManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Executes migrations, that was not executed.')
            ->addArgument('alias', InputArgument::OPTIONAL, 'Aliases: available: prev - for reverting previous migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $alias = $input->getArgument('alias');

        if ('prev' === $alias) {
            $migration = $this->migrationManager->getLatestExecutedMigrationName();
            $this->migrationManager->down($migration);

            return Command::SUCCESS;
        }

        $this->migrationManager->execute();

        return Command::SUCCESS;
    }
}
