<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Marketplace;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Location\Location;
use App\Infrastructure\DataGrid\Marketplace\LocationList;
use App\Infrastructure\Repository\Company\CompanyRepository;
use App\Infrastructure\Repository\LocationRepository;
use App\Tests\Unit\UnitTestCase;

class LocationListTest extends UnitTestCase
{
    public function testItTransformsLocationIntoArray(): void
    {
        $companyId = 'zz5x-ghr6-dd6y-hh53';

        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn($companyId);

        $id = '61f021a163b571290822cde1';
        $name = 'location name';
        $reference = 'reference';
        $address1 = 'address1';
        $address2 = 'address1';
        $city = 'London';
        $postalCode = 'postal-code';

        $location = $this->createStub(Location::class);
        $location->method('getId')->willReturn($id);
        $location->method('getCompany')->willReturn($company);
        $location->method('getName')->willReturn($name);
        $location->method('getReference')->willReturn($reference);
        $location->method('getAddress1')->willReturn($address1);
        $location->method('getAddress2')->willReturn($address2);
        $location->method('getCity')->willReturn($city);
        $location->method('getPostalCode')->willReturn($postalCode);

        $locationList = new LocationList($this->createStub(LocationRepository::class), $this->createStub(CompanyRepository::class));

        self::assertSame([
            'id' => $id,
            'merchantId' => $companyId,
            'name' => $name,
            'reference' => $reference,
            'address' => [
                'address1' => $address1,
                'address2' => $address2,
                'city' => $city,
                'country' => null,
                'state' => null,
                'postalCode' => $postalCode,
            ],
        ], $locationList->transform($location, 0));
    }
}
