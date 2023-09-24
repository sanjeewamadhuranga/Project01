<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\DataGrid;

use App\Application\DataGrid\DataGrid;
use App\Domain\DataGrid\Filters\TransactionFilters;
use App\Infrastructure\DataGrid\RequestFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RequestFactoryTest extends KernelTestCase
{
    private RequestFactory $requestFactory;

    protected function setUp(): void
    {
        $this->requestFactory = new RequestFactory(self::getContainer()->get(DenormalizerInterface::class));
    }

    public function testItTransfersStringToIntWhenPropertyExpectsInteger(): void
    {
        $amount = '400';

        $request = $this->createStub(Request::class);
        $request->query = new InputBag();
        $request->query->add([
            'filters' => ['amount' => $amount],
        ]);

        $dataGrid = $this->createStub(DataGrid::class);
        $dataGrid->method('getFilterDto')->willReturn(TransactionFilters::class);

        /** @var TransactionFilters $filters */
        $filters = $this->requestFactory->getGridRequest($request, $dataGrid)->getFilters();

        self::assertSame((int) $amount, $filters->getAmount());
    }
}
