<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\DataGrid\Filters;

use App\Application\DataGrid\Filters\SortDirection;
use App\Tests\Unit\UnitTestCase;

class SortDirectionTest extends UnitTestCase
{
    public function testItOppositesSortingDirection(): void
    {
        self::assertSame(SortDirection::DESC, SortDirection::ASC->opposite());
        self::assertSame(SortDirection::ASC, SortDirection::DESC->opposite());
    }
}
