<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataCollector;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\Filters\Filters;
use App\Application\DataGrid\Filters\GridRequest;
use App\Infrastructure\DataCollector\DataGridDataCollector;
use App\Infrastructure\DataGrid\Debug\DataGridCall;
use App\Infrastructure\DataGrid\Debug\TraceableDataGridHandler;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataGridDataCollectorTest extends UnitTestCase
{
    public function testItCopyAndStoresCallsFromDataGridHandlerInDataProperty(): void
    {
        /** @var GridRequest<Filters> $gridRequest */
        $gridRequest = $this->createStub(GridRequest::class);
        $call = new DataGridCall($this->createStub(DataGrid::class));
        $call->gridRequest = $gridRequest;

        $dataGridHandler = $this->createStub(TraceableDataGridHandler::class);
        $dataGridHandler->method('getCalls')->willReturn([$call]);

        $dataGridCollector = new DataGridDataCollector($dataGridHandler);

        $dataGridCollector->collect($this->createStub(Request::class), $this->createStub(Response::class));

        $collectorCalls = $dataGridCollector->getCalls();
        /** @var array<int, mixed> $values */
        $values = $collectorCalls->getValue(true);
        self::assertSame([[
            'gridRequest' => [
                "\x00~\x00sorting" => [],
                "\x00~\x00limit" => 0,
                "\x00~\x00offset" => 0,
                "\x00~\x00filters" => null,
            ],
            'dataGrid' => [],
        ]], $values);
    }

    public function testItClearsDataAndDataGridHandler(): void
    {
        $calls = [$this->createStub(DataGridCall::class)];

        $dataGridHandler = $this->createMock(TraceableDataGridHandler::class);
        $dataGridHandler->method('getCalls')->willReturn($calls);
        $dataGridHandler->expects(self::once())->method('clearCalls');

        $dataGridCollector = new DataGridDataCollector($dataGridHandler);
        $dataGridCollector->collect($this->createStub(Request::class), $this->createStub(Response::class));

        /** @var array<int, mixed> $values */
        $values = $dataGridCollector->getCalls()->getValue(true);
        self::assertCount(1, $values);
        $dataGridCollector->reset();
        self::assertEmpty($dataGridCollector->getCalls()->getValue(true));
    }

    public function testItReturnsTemplatePath(): void
    {
        self::assertSame('data_collector/data_grid.html.twig', DataGridDataCollector::getTemplate());
    }
}
