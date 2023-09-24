<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Application\DataGrid\Filters\GridRequest;
use App\Infrastructure\DataGrid\SimpleMongoDataGrid;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\FilterCollection;
use Doctrine\ODM\MongoDB\Query\Query;
use stdClass;

class SimpleMongoDataGridTest extends UnitTestCase
{
    public function testSampleMongoDataGridTransform(): void
    {
        $class = new stdClass();
        $class->name = 'test';

        $documentManager = $this->createStub(DocumentManager::class);
        $simpleMongoDataGrid = $this->getMockForAbstractClass(SimpleMongoDataGrid::class, [$documentManager]);
        self::assertSame($class, $simpleMongoDataGrid->transform($class, 0));
    }

    public function testItReturnExpectedDataGrid(): void
    {
        $class = new stdClass();
        $class->name = 'test';

        $documentManager = $this->createMock(DocumentManager::class);
        $classMetaData = $this->createMock(ClassMetadata::class);
        $classMetaData->name = stdClass::class;
        $documentManager->method('getClassMetadata')->willReturn($classMetaData);
        $documentManager->method('getFilterCollection')->willReturn(new FilterCollection($documentManager));
        $documentManager->expects(self::once())
            ->method('createQueryBuilder')
            ->with($class::class)
            ->willReturn(new Builder($documentManager, 'test'));
        $documentManager->method('getUnitOfWork')->willReturn($this->getUnitOfWork($documentManager));

        $simpleMongoDataGrid = $this->getMockForAbstractClass(SimpleMongoDataGrid::class, [$documentManager]);
        $simpleMongoDataGrid->method('getItemType')->willReturn($class::class);

        $resultSet = $simpleMongoDataGrid->getData($this->createStub(GridRequest::class));
        $query = $resultSet->getItems();
        $pagination = $resultSet->getPaginationInfo();

        self::assertInstanceOf(Query::class, $query);
        self::assertSame(Query::TYPE_FIND, $query->getType());
        self::assertSame(['deleted' => ['$in' => [false, null]]], $query->debug('query'));
        self::assertSame(-1, $pagination->getPerPage());
        self::assertFalse($pagination->hasNextPage());
        self::assertFalse($pagination->hasPreviousPage());
    }
}
