<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use App\Domain\Document\BaseDocument;
use App\Infrastructure\Repository\BaseRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use UnexpectedValueException;

class DocumentExtension extends AbstractExtension
{
    public function __construct(private readonly DocumentManager $dm)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('raw_document', $this->getRawDocument(...)),
        ];
    }

    /**
     * @template T of BaseDocument
     *
     * @psalm-param T $document
     */
    public function getRawDocument(BaseDocument $document): ?object
    {
        /** @var DocumentRepository<T> $repository */
        $repository = $this->dm->getRepository($document::class);

        if (!$repository instanceof BaseRepository) {
            throw new UnexpectedValueException(sprintf('Repository of %s should be an instance of %s', $document::class, BaseRepository::class));
        }

        $object = $repository->getRawData($document);

        return null === $object ? null : (object) $object;
    }
}
