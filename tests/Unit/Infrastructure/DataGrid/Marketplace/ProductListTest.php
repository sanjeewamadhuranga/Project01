<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Marketplace;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Location\Location;
use App\Domain\Document\Product;
use App\Infrastructure\DataGrid\Marketplace\ProductList;
use App\Infrastructure\Repository\ProductRepository;
use App\Tests\Unit\UnitTestCase;

class ProductListTest extends UnitTestCase
{
    public function testItTransformsProductIntoArray(): void
    {
        $companyId = 'tt5e-tt5a-tt5v-tt5c';
        $locationId = '5beb13f49006e8027dacd105';

        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn($companyId);

        $location = $this->createStub(Location::class);
        $location->method('getId')->willReturn($locationId);

        $id = '61f021dc44ade122460c47ad';
        $name = 'product name';
        $description = 'some description';
        $price = 50000;
        $currency = 'USD';

        $product = $this->createStub(Product::class);
        $product->method('getCompany')->willReturn($company);
        $product->method('getId')->willReturn($id);
        $product->method('getName')->willReturn($name);
        $product->method('getDescription')->willReturn($description);
        $product->method('getPrice')->willReturn($price);
        $product->method('getCurrency')->willReturn($currency);
        $product->method('getLocation')->willReturn($location);

        $productList = new ProductList(
            $this->createStub(ProductRepository::class)
        );

        self::assertSame([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'merchantId' => $companyId,
            'currency' => $currency,
            'locationId' => $locationId,
        ], $productList->transform($product, 0));
    }
}
