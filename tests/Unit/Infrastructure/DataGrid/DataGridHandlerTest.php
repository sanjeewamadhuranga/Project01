<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\DataGridHandlerInterface;
use App\Application\DataGrid\Filters\Filters;
use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\Pagination;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Application\DataGrid\ResultSet;
use App\Infrastructure\DataGrid\DataGridHandler;
use App\Infrastructure\DataGrid\RequestFactory;
use App\Infrastructure\DataGrid\ResponseFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataGridHandlerTest extends UnitTestCase
{
    protected RequestFactory&MockObject $requestFactory;

    protected ResponseFactory&MockObject $responseFactory;

    protected DataGridHandlerInterface $dataGridHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestFactory = $this->createMock(RequestFactory::class);
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->dataGridHandler = new DataGridHandler($this->requestFactory, $this->responseFactory);
    }

    public function testItCallsDataGridAndConvertsResponse(): void
    {
        $request = new Request();
        $response = $this->createStub(Response::class);
        $grid = $this->createMock(DataGrid::class);
        $resultSet = $this->createStub(ResultSet::class);
        $gridRequest = new GridRequest(
            $this->createStub(Filters::class),
            new Sorting('id', SortDirection::ASC),
            new Pagination(150, 50)
        );
        $this->requestFactory->expects(self::once())->method('getGridRequest')->with($request, $grid)->willReturn($gridRequest);
        $grid->expects(self::once())->method('getData')
            ->with($gridRequest)
            ->willReturn($resultSet);
        $this->responseFactory->expects(self::once())->method('getResponse')->with($request, $resultSet, $grid)->willReturn($response);

        self::assertSame($response, $this->dataGridHandler->__invoke($request, $grid));
    }
}
