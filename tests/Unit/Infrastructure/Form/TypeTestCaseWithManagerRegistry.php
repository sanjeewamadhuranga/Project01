<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\Persistence\ManagerRegistry;
use MongoDB\Collection;
use Symfony\Component\Form\Test\TypeTestCase;

abstract class TypeTestCaseWithManagerRegistry extends TypeTestCase
{
    /**
     * @param DocumentRepository<object>|null $repository
     */
    protected function getManagerRegistry(?DocumentRepository $repository = null): ManagerRegistry
    {
        $repository ??= $this->getRepository();

        $documentManager = $this->createStub(DocumentManager::class);
        $documentManager->method('getRepository')->willReturn($repository);

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($documentManager);
        $registry->method('getManagerForClass')->willReturn($documentManager);

        return $registry;
    }

    /**
     * @param object[] $items
     *
     * @return DocumentRepository<object>
     */
    protected function getRepository(array $items = []): DocumentRepository
    {
        $collection = $this->createStub(Collection::class);
        $collection->method('find')->willReturn(new ArrayCollection($items));

        $query = new Query(
            $this->createStub(DocumentManager::class),
            $this->createStub(ClassMetadata::class),
            $collection,
            [
                'type' => Query::TYPE_FIND,
                'query' => [],
            ],
            hydrate: false,
        );

        $builder = $this->createStub(Builder::class);
        $builder->method('getQuery')->willReturn($query);

        $repository = $this->createStub(DocumentRepository::class);
        $repository->method('createQueryBuilder')->willReturn($builder);

        return $repository;
    }
}
