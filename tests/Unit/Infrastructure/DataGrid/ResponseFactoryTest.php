<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\Pagination\LengthAwarePaginationInfo;
use App\Application\DataGrid\ResultSet;
use App\Application\DataGrid\TransformableGrid;
use App\Infrastructure\DataGrid\ResponseFactory;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseFactoryTest extends UnitTestCase
{
    private SerializerInterface $serializer;

    private ResponseFactory $responseFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->responseFactory = new ResponseFactory($this->serializer);
    }

    public function testItSerializesDataFromResultSetAndReturnsDrawInput(): void
    {
        $request = new Request(['draw' => 3]);
        $expectedResponse = [
            'draw' => 3,
            'data' => ['test', 'test2'],
            'pagination' => [
                'filteredCount' => 10,
                'totalCount' => 100,
                'perPage' => 25,
                'nextPage' => false,
                'previousPage' => false,
                'type' => 'length_aware',
            ],
        ];
        $grid = $this->createStub(DataGrid::class);

        $response = $this->responseFactory->getResponse(
            $request,
            new ResultSet(['test', 'test2'], new LengthAwarePaginationInfo(25, 0, 10, 100)),
            $grid
        );
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame($expectedResponse, json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testItReturnsZeroDrawIfItIsMissing(): void
    {
        $request = new Request();
        $expectedResponse = [
            'draw' => 0,
            'data' => [],
            'pagination' => [
                'filteredCount' => 10,
                'totalCount' => 100,
                'perPage' => 25,
                'nextPage' => false,
                'previousPage' => false,
                'type' => 'length_aware',
            ],
        ];
        $grid = $this->createStub(DataGrid::class);

        $response = $this->responseFactory->getResponse($request, new ResultSet([], new LengthAwarePaginationInfo(25, 0, 10, 100)), $grid);
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame($expectedResponse, json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testItTransformsDataIfNeeded(): void
    {
        $request = new Request();
        $grid = $this->createMock(TransformableDataGrid::class);
        $grid->expects(self::exactly(2))->method('transform')->willReturnCallback(fn (string $item) => strtoupper($item));

        $response = $this->responseFactory->getResponse($request, new ResultSet(['test', 'test2'], new LengthAwarePaginationInfo(25, 0, 10, 100)), $grid);
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(['TEST', 'TEST2'], json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR)['data'] ?? []);
    }
}

/**
 * @implements TransformableGrid<\App\Application\DataGrid\Filters\Filters, object>
 */
abstract class TransformableDataGrid implements TransformableGrid
{
}
