<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\DataGrid;

use App\Application\DataGrid\Filters\Filters;
use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\Pagination;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Application\DataGrid\TransformableGrid;
use App\Tests\Feature\BaseTestCase;

abstract class BaseDataGridTest extends BaseTestCase
{
    /**
     * @var TransformableGrid<Filters, object>
     */
    protected readonly TransformableGrid $list;

    protected function setUp(): void
    {
        parent::setUp();
        $this->list = $this->getDataGrid();
    }

    /**
     * @return TransformableGrid<Filters, object>
     */
    abstract protected function getDataGrid(): TransformableGrid;

    /**
     * @return GridRequest<Filters>
     */
    protected function getRequestForFilters(?Filters $filters): GridRequest
    {
        return new GridRequest($filters, new Sorting(null, SortDirection::ASC), new Pagination(0, 10));
    }
}
