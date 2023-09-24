<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Application\DataGrid\Filters\GridRequest;
use App\Infrastructure\DataGrid\FixedItemsDataGrid;
use App\Tests\Unit\UnitTestCase;
use stdClass;

class FixedItemDataGridTest extends UnitTestCase
{
    public function testItNormalTransform(): void
    {
        self::assertSame('test', (new FixedItemsDataGrid([]))->transform('test', 0));
    }

    public function testItCallClosureAndTransform(): void
    {
        $dataGrid = new FixedItemsDataGrid([], fn ($item) => strtoupper($item));
        self::assertSame('TEST', $dataGrid->transform('test', 0));
    }

    public function testItWillReturnFixedItemDataGridAsAResultSet(): void
    {
        $class = new stdClass();
        $class->name = 'test';

        $gridRequest = $this->createStub(GridRequest::class);
        $fixedItemsDataGrid = (new FixedItemsDataGrid([$class]))->getData($gridRequest);

        self::assertSame([$class], $fixedItemsDataGrid->getItems());
        self::assertSame(-1, $fixedItemsDataGrid->getPaginationInfo()->getPerPage());
        self::assertFalse($fixedItemsDataGrid->getPaginationInfo()->hasNextPage());
        self::assertFalse($fixedItemsDataGrid->getPaginationInfo()->hasPreviousPage());
    }
}
