<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Debug;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\DataGridHandlerInterface;
use App\Infrastructure\DataCollector\DataGridDataCollector;
use App\Infrastructure\DataGrid\DataGridHandler;
use App\Infrastructure\DataGrid\RequestFactory;
use App\Infrastructure\DataGrid\ResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Traceable handler that works just like {@see DataGridHandler} but logs all requests for debugging purposes.
 * It is used only on dev environment.
 *
 * @see DataGridDataCollector
 */
class TraceableDataGridHandler implements DataGridHandlerInterface
{
    /** @var DataGridCall[] */
    private array $calls = [];

    public function __construct(private readonly RequestFactory $requestFactory, private readonly ResponseFactory $responseFactory)
    {
    }

    public function __invoke(Request $request, DataGrid $dataGrid): Response
    {
        $call = new DataGridCall($dataGrid);
        $this->calls[] = $call;
        $gridRequest = $this->requestFactory->getGridRequest($request, $dataGrid);
        $call->gridRequest = $gridRequest;
        $result = $dataGrid->getData($gridRequest);

        return $this->responseFactory->getResponse($request, $result, $dataGrid);
    }

    /**
     * @return DataGridCall[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    public function clearCalls(): void
    {
        $this->calls = [];
    }
}
