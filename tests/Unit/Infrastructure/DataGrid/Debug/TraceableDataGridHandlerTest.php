<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Debug;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\DataGridHandlerInterface;
use App\Application\DataGrid\Filters\Filters;
use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\Pagination;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Application\DataGrid\ResultSet;
use App\Infrastructure\DataGrid\Debug\TraceableDataGridHandler;
use App\Infrastructure\DataGrid\RequestFactory;
use App\Infrastructure\DataGrid\ResponseFactory;
use App\Tests\Unit\Infrastructure\DataGrid\DataGridHandlerTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TraceableDataGridHandlerTest extends DataGridHandlerTest
{
    protected DataGridHandlerInterface $dataGridHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestFactory = $this->createMock(RequestFactory::class);
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->dataGridHandler = new TraceableDataGridHandler($this->requestFactory, $this->responseFactory);
    }

    public function testItCollectsCalls(): void
    {
        $request = new Request();
        $gridRequest = new GridRequest(
            $this->createStub(Filters::class),
            new Sorting('test', SortDirection::ASC),
            new Pagination(200, 10)
        );
        $response = $this->createStub(Response::class);
        $grid = $this->createMock(DataGrid::class);
        $resultSet = $this->createStub(ResultSet::class);
        $this->requestFactory->expects(self::once())->method('getGridRequest')->with($request, $grid)->willReturn($gridRequest);
        $grid->expects(self::once())->method('getData')
            ->with($gridRequest)
            ->willReturn($resultSet);
        $this->responseFactory->expects(self::once())->method('getResponse')->with($request, $resultSet, $grid)->willReturn($response);

        self::assertInstanceOf(TraceableDataGridHandler::class, $this->dataGridHandler);
        self::assertSame($response, $this->dataGridHandler->__invoke($request, $grid));

        $calls = $this->dataGridHandler->getCalls();
        self::assertArrayHasKey(0, $calls);
        // @phpstan-ignore-next-line False-positive
        self::assertSame($gridRequest, $calls[0]->gridRequest);
        self::assertSame($grid, $calls[0]->dataGrid);
    }
}
