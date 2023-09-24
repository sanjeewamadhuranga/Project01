<?php

declare(strict_types=1);

namespace App\Application\DataGrid;

use App\Application\DataGrid\Filters\Filters;
use App\Application\DataGrid\Filters\GridRequest;
use App\Infrastructure\DataGrid\RequestFactory;
use App\Infrastructure\DataGrid\ResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * It handles a {@see Request}, creates filters, passes them to {@see DataGrid} and returns a HTTP {@see Response}.
 * This is to completely decouple DataGrid logic from HTTP layer.
 *
 * The usual flow for DataGrid is:
 *
 * 1. Controller calls the handler to prepare the response {@see BaseController::handleDataGrid()}.
 * 2. DataGridHandler {@uses RequestFactory} to prepare a {@see GridRequest} from HTTP {@see Request}.
 * 3. RequestFactory handles the form defined in {@see DataGrid::getFilterDto()} to build the {@see Filters}.
 * 4. DataGridHandler passes the {@see GridRequest} to the {@see DataGrid::getData()}.
 * 5. The result is passed to {@see ResponseFactory} which converts {@see Result} into a HTTP {@see Response}.
 *
 * This flow allows to plug into any part of the process and modify data if needed as well as transparently modify
 * the contract with frontend (ie. pagination parameter names).
 */
interface DataGridHandlerInterface
{
    /**
     * @param DataGrid<Filters> $dataGrid
     */
    public function __invoke(Request $request, DataGrid $dataGrid): Response;
}
