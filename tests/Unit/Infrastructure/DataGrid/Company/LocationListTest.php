<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Company;

use App\Domain\Company\LocationStatus;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Location\Location;
use App\Infrastructure\DataGrid\Company\LocationList;
use App\Infrastructure\Repository\ProductRepository;
use App\Tests\Unit\UnitTestCase;

class LocationListTest extends UnitTestCase
{
    public function testItTransformsLocationIntoArray(): void
    {
        $id = '620114bf9b21700d7b67850b';
        $name = 'LocationName';
        $status = LocationStatus::OPEN;
        $location = $this->getLocation($id, $name, $status);

        $numberOfProducts = 58;
        $productRepository = $this->createStub(ProductRepository::class);
        $productRepository->method('countForLocation')->willReturn($numberOfProducts);

        self::assertSame([
            'id' => $id,
            'name' => $name,
            'status' => $status,
            'productNumber' => $numberOfProducts,
        ], (new LocationList($this->createStub(Company::class), $productRepository))->transform($location, 0));
    }

    private function getLocation(string $id, string $name, LocationStatus $status): Location
    {
        $location = new Location();
        $location->setId($id);
        $location->setName($name);
        $location->setStatus($status);

        return $location;
    }
}
