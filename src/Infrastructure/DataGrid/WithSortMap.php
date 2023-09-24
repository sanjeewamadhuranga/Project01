<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid;

trait WithSortMap
{
    /**
     * Returns the sort column map: [frontend_column_name => fieldName].
     *
     * @return array<string|null, string>
     */
    protected function getSortMap(): array
    {
        return [
            'id' => 'id',
        ];
    }
}
