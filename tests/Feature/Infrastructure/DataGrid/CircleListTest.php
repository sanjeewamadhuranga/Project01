<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\DataGrid;

use App\Application\DataGrid\TransformableGrid;
use App\Domain\DataGrid\Filters\BasicFilters;
use App\Infrastructure\DataGrid\Company\CircleList;

class CircleListTest extends BaseDataGridTest
{
    public function testApplyEmptyFilters(): void
    {
        $filters = new BasicFilters();
        $data = $this->list->getData($this->getRequestForFilters($filters));
        self::assertCount(6, iterator_to_array($data->getIterator()));
    }

    protected function getDataGrid(): TransformableGrid
    {
        return self::$client->getContainer()->get(CircleList::class); // @phpstan-ignore-line
    }
}
