<?php

declare(strict_types=1);

namespace App\Application\DataGrid\Filters;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public function opposite(): self
    {
        return self::ASC === $this ? self::DESC : self::ASC;
    }
}
