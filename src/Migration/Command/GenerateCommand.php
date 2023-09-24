<?php

declare(strict_types=1);

namespace App\Migration\Command;

use App\Migration\MigrationGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:migrations:generate')]
class GenerateCommand extends Command
{
    public function __construct(private readonly MigrationGenerator $migrationGenerator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generates new, empty migration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');
        $migrationName = $this->migrationGenerator->generateMigrationFile();
        $output->writeln($formatter->formatBlock(sprintf('%s has been generated.', $migrationName), 'info'));

        return self::SUCCESS;
    }
}
