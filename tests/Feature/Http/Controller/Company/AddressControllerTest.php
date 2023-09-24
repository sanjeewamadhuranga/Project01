<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Domain\Document\Company\Address;
use App\Tests\Feature\BaseTestCase;

class AddressControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTouchesDb();
    }

    public function testEditRegisteredAddress(): void
    {
        $testCompany = $this->getTestCompany();

        self::$client->request('GET', sprintf('/merchants/%s/update-address/registered', $testCompany->getId()));
        self::assertSelectorTextContains('html', 'Registered address');

        self::$client->submitForm('Submit', [
            'address' => [
                'buildingName' => 'registered building name',
                'buildingNumber' => 'registered building number',
                'flatNumber' => 'registered flat number',
                'street' => 'registered street',
                'town' => 'registered town',
                'country' => 'GBR',
                'postCode' => '123456',
            ],
        ]);

        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);

        $registeredAddress = $testCompany->getRegisteredAddress();
        self::assertInstanceOf(Address::class, $registeredAddress);
        self::assertSame('registered building name', $registeredAddress->getBuildingName());
        self::assertSame('registered building number', $registeredAddress->getBuildingNumber());
        self::assertSame('registered flat number', $registeredAddress->getFlatNumber());
        self::assertSame('registered street', $registeredAddress->getStreet());
        self::assertSame('registered town', $registeredAddress->getTown());
        self::assertSame('GBR', $registeredAddress->getCountry());
        self::assertSame('123456', $registeredAddress->getPostCode());
    }

    public function testEditTradingAddress(): void
    {
        $testCompany = $this->getTestCompany();

        self::$client->request('GET', sprintf('/merchants/%s/update-address/trading', $testCompany->getId()));
        self::assertSelectorTextContains('html', 'Trading address');

        self::$client->submitForm('Submit', [
            'address' => [
                'buildingName' => 'Trading address building name',
                'buildingNumber' => 'Trading address building number',
                'flatNumber' => 'Trading address flat number',
                'street' => 'Trading address street',
                'town' => 'Trading address town',
                'country' => 'GBR',
                'postCode' => '123456',
            ],
        ]);

        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        $tradingAddress = $testCompany->getTradingAddress();

        self::assertInstanceOf(Address::class, $tradingAddress);
        self::assertSame('Trading address building name', $tradingAddress->getBuildingName());
        self::assertSame('Trading address building number', $tradingAddress->getBuildingNumber());
        self::assertSame('Trading address flat number', $tradingAddress->getFlatNumber());
        self::assertSame('Trading address street', $tradingAddress->getStreet());
        self::assertSame('Trading address town', $tradingAddress->getTown());
        self::assertSame('GBR', $tradingAddress->getCountry());
        self::assertSame('123456', $tradingAddress->getPostCode());
    }

    public function testEditCorrespondenceAddress(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s/update-address/correspondence', $testCompany->getId()));
        self::assertSelectorTextContains('html', 'Correspondence address');

        self::$client->submitForm('Submit', [
            'correspondence_address' => [
                'address1' => 'Test address 1',
                'address2' => 'Test address 2',
                'postalCode' => '12345',
                'city' => 'Test city',
                'state' => 'Test state',
            ],
        ]);

        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);

        self::assertSame('Test address 1', $testCompany->getAddress1());
        self::assertSame('Test address 2', $testCompany->getAddress2());
        self::assertSame('12345', $testCompany->getPostalCode());
        self::assertSame('Test state', $testCompany->getState());
    }
}
