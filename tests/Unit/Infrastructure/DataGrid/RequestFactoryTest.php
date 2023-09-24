<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\Filters\Filters;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Infrastructure\DataGrid\RequestFactory;
use App\Infrastructure\Serializer\DataGridSanitizer;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RequestFactoryTest extends UnitTestCase
{
    private RequestFactory $requestFactory;

    private DenormalizerInterface&MockObject $denormalizer;

    public function setUp(): void
    {
        parent::setUp();

        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->requestFactory = new RequestFactory($this->denormalizer);
    }

    public function testItDoesNotPassFiltersIfRequestHasNoData(): void
    {
        $grid = $this->createGridStub();
        $this->expectDenormalizerCall([]);

        self::assertNull($this->requestFactory->getGridRequest(new Request(), $grid)->getFilters());
    }

    /**
     * @dataProvider sortingRequestProvider
     */
    public function testItHandlesSorting(Request $request, Sorting $expectedSorting): void
    {
        $grid = $this->createGridStub();
        $this->expectDenormalizerCall($request->query->all('filters'));

        self::assertEquals($expectedSorting, $this->requestFactory->getGridRequest($request, $grid)->getSorting());
    }

    /**
     * @dataProvider paginationRequestProvider
     */
    public function testItHandlesPagination(Request $request, int $offset, int $limit): void
    {
        $filters = $this->createStub(Filters::class);
        $this->expectDenormalizerCall($request->query->all('filters'), $filters);

        $grid = $this->createGridStub();
        $gridRequest = $this->requestFactory->getGridRequest($request, $grid);

        self::assertSame($filters, $gridRequest->getFilters());
        self::assertSame($offset, $gridRequest->getOffset());
        self::assertSame($limit, $gridRequest->getLimit());
    }

    /**
     * @return array<string, array{Request, int ,int}>
     */
    public function paginationRequestProvider(): array
    {
        return [
            'No pagination data provided' => [new Request(), 0, 25],
            'Only limit provided' => [new Request(['length' => 150]), 0, 150],
            'Both limit and offset provided' => [new Request(['start' => 25, 'length' => 150]), 25, 150],
        ];
    }

    /**
     * @return array<string, array{Request, Sorting}>
     */
    public function sortingRequestProvider(): array
    {
        return [
            'no sorting provided' => [
                new Request(),
                new Sorting(null, SortDirection::ASC),
            ],
            'sort_column and sort_dir provided' => [
                new Request(['sort_column' => 'tradingName', 'sort_dir' => 'desc']),
                new Sorting('tradingName', SortDirection::DESC),
            ],
            'order (DataTables format)' => [
                new Request([
                    'order' => [['column' => 2, 'dir' => 'asc']],
                    'columns' => [
                        ['data' => 'id'],
                        ['data' => 'tradingName'],
                        ['data' => 'provider'],
                    ],
                ]),
                new Sorting('provider', SortDirection::ASC),
            ],
            'order (DataTables format) when column is out of bounds' => [
                new Request([
                    'order' => [['column' => 1, 'dir' => 'desc']],
                    'columns' => [['data' => 'tradingName']],
                ]),
                new Sorting(null, SortDirection::DESC),
            ],
        ];
    }

    /**
     * @return DataGrid<Filters>&Stub
     */
    private function createGridStub(): DataGrid
    {
        $grid = $this->createStub(DataGrid::class);
        $grid->method('getFilterDto')->willReturn('TestFilters');

        return $grid;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function expectDenormalizerCall(array $data = [], ?object $filters = null): void
    {
        $this->denormalizer->expects(self::once())->method('denormalize')->with(
            $data,
            'TestFilters',
            null,
            [
                DataGridSanitizer::SANITIZE_INPUT => true,
                AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
            ]
        )->willReturn($filters);
    }
}
