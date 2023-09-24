<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Configuration;

use App\Domain\Document\Country;
use App\Infrastructure\Repository\CountryRepository;
use App\Tests\Feature\BaseTestCase;

class CountryControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsCountryList(): void
    {
        self::$client->request('GET', '/configuration/country');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('config-country-list');
    }

    /**
     * @group smoke
     */
    public function testItListsCountries(): void
    {
        self::$client->request('GET', '/configuration/country/list');

        $this->assertGridResponse();
    }

    /**
     * @group smoke
     */
    public function testItShowsCountryDetails(): void
    {
        /** @var Country $testCountry */
        $testCountry = self::$fixtures['country_test'];
        self::$client->request('GET', sprintf('/configuration/country/%s', $testCountry->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'test');
    }

    public function testItAllowsToDeleteACountry(): void
    {
        $this->markTouchesDb();
        /** @var Country $testCountry */
        $testCountry = self::$fixtures['country_test'];
        self::$client->request('DELETE', sprintf('/configuration/country/%s/delete', $testCountry->getId()));

        self::assertResponseIsSuccessful();
        $testCountry = $this->getDocumentManager()->find(Country::class, $testCountry->getId());
        self::assertInstanceOf(Country::class, $testCountry);
        self::assertTrue($this->refresh($testCountry)->isDeleted());
    }

    public function testItCreatesCountry(): void
    {
        $this->markTouchesDb();
        self::$client->request('GET', '/configuration/country/create');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Create Country');

        self::$client->submitForm('Submit', [
            'country' => [
                'countryCode' => 'SG',
                'dialingCode' => '+65',
            ],
        ]);

        $country = self::getContainer()->get(CountryRepository::class)->findOneBy(['countryCode' => 'SG']);
        self::assertInstanceOf(Country::class, $country);
    }

    public function testItUpdateCountry(): void
    {
        $this->markTouchesDb();
        /** @var Country $testCountry */
        $testCountry = self::$fixtures['country_test'];

        self::$client->request('GET', sprintf('/configuration/country/%s/edit', $testCountry->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Update Country');

        self::$client->submitForm('Submit', [
            'country' => [
                'countryCode' => 'SG',
                'dialingCode' => '+66',
            ],
        ]);

        $testCountry = $this->refresh($testCountry);
        self::assertSame('SG', $testCountry->getCountryCode());
        self::assertSame('+66', $testCountry->getDialingCode());
    }
}
