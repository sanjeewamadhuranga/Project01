<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Exception;
use MongoDB\Model\CollectionInfoIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When('dev')]
class DatabaseComparisonCommand extends Command
{
    public function __construct(private readonly DocumentManager $documentManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('This command will allow you to list mapped and unmapped MongoDB collections.')
            ->setName('app:database:comparison')
            ->addOption('unmapped', null, InputOption::VALUE_NEGATABLE, 'Filter to show lists of class by providing either --unmapped or --no-unmapped. By default it will show all.');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $unmapped = $input->getOption('unmapped');
        $io = new SymfonyStyle($input, $output);
        $documents = [...$this->getDocuments()];

        $rows = [];
        $unmappedCollections = 0;
        foreach ($this->getCollections() as $collection) {
            /** @var ClassMetadata|null $document */
            $document = $documents[$collection->getName()] ?? null;

            if (null === $document) {
                ++$unmappedCollections;
            }

            if (null !== $unmapped && ($unmapped xor null === $document)) {
                continue;
            }

            $documentClass = $document?->getName();

            $rows[] = [
                $collection->getName(),
                sprintf('<fg=%s>%s</>', 'collection' === $collection->getType() ? 'magenta' : 'cyan', $collection->getType()),
                (null === $documentClass)
                    ? '<fg=yellow>n/a</>'
                    : sprintf('<fg=green>%s</>', $documentClass),
                $this->renderBool(null !== $document),
                $this->renderBool($document->isReadOnly ?? false),
            ];
        }

        $io->createTable()->setStyle('box')
            ->setHeaderTitle('Database comparison')
            ->setHeaders(['Collection name', 'Type', 'Document class', 'Mapped?', 'Read-only?'])
            ->setRows($rows)
            ->render();

        if ($unmappedCollections > 0) {
            $io->warning(sprintf('There are %d unmapped collections!', $unmappedCollections));

            return Command::SUCCESS;
        }

        $io->success('There are no unmapped collections!');

        return Command::SUCCESS;
    }

    /**
     * @return iterable<string, ClassMetadata>
     */
    private function getDocuments(): iterable
    {
        foreach ($this->documentManager->getMetadataFactory()->getAllMetadata() as $metadata) {
            if (!$metadata->isQueryResultDocument && !$metadata->isEmbeddedDocument && !$metadata->isMappedSuperclass) {
                yield $metadata->getCollection() => $metadata;
            }
        }
    }

    private function getCollections(): CollectionInfoIterator
    {
        return $this->documentManager->getClient()
            ->selectDatabase((string) $this->documentManager->getConfiguration()->getDefaultDB())
            ->listCollections();
    }

    private function renderBool(bool $input): string
    {
        return $input ? '<fg=green>✓</>' : '<fg=red>⨯</>';
    }
}
