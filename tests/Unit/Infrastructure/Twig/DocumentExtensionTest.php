<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Twig;

use App\Domain\Document\BaseDocument;
use App\Infrastructure\Repository\BaseRepository;
use App\Infrastructure\Twig\DocumentExtension;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Exception;
use stdClass;

class DocumentExtensionTest extends UnitTestCase
{
    public function testItRegistersFilters(): void
    {
        $dm = $this->createStub(DocumentManager::class);
        $extension = new DocumentExtension($dm);
        $filters = $extension->getFilters();

        self::assertCount(1, $filters);
        self::assertSame('raw_document', $filters[0]->getName());
    }

    public function testItReturnsNullIfNoDocumentFound(): void
    {
        $repository = $this->createMock(BaseRepository::class);
        $repository->expects(self::once())->method('getRawData')->willReturn(null);

        $extension = $this->getDocumentExtension($repository);
        self::assertNull($extension->getRawDocument($this->createStub(BaseDocument::class)));
    }

    public function testItThrowsExceptionWhenRepositoryDoNotExtendsBaseRepository(): void
    {
        $repository = $this->createMock(DocumentRepository::class);
        $extension = $this->getDocumentExtension($repository);

        self::expectException(Exception::class);
        $extension->getRawDocument($this->createStub(BaseDocument::class));
    }

    public function testItReturnsObjectIfDocumentFound(): void
    {
        $object = new stdClass();
        $repository = $this->createMock(BaseRepository::class);
        $repository->expects(self::once())->method('getRawData')->willReturn($object);

        $extension = $this->getDocumentExtension($repository);
        self::assertSame($object, $extension->getRawDocument($this->createStub(BaseDocument::class)));
    }

    /**
     * @template T of object
     *
     * @param DocumentRepository<T> $repository
     */
    private function getDocumentExtension(DocumentRepository $repository): DocumentExtension
    {
        $dm = $this->createMock(DocumentManager::class);
        $dm->expects(self::once())->method('getRepository')->willReturn($repository);

        return new DocumentExtension($dm);
    }
}
