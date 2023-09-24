<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Search;

use App\Application\Search\SearchQuery;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class SearchQueryTest extends UnitTestCase
{
    public function testItThrowsExceptionWhenQueryIsTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SearchQuery('ab');
    }

    public function testItReturnsQueryString(): void
    {
        $query = 'query';
        $searchQuery = new SearchQuery($query);

        self::assertSame($query, $searchQuery->getQueryString());
    }

    public function testItCreatesSearchQueryFromRequest(): void
    {
        $query = 'someSearch';
        $request = $this->createMock(Request::class);
        $request->expects(self::once())->method('get')->with('query')->willReturn($query);

        $searchQuery = SearchQuery::fromRequest($request);

        self::assertSame($query, $searchQuery->getQueryString());
    }
}
