<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Tests\Feature\BaseTestCase;

class QuickSearchControllerTest extends BaseTestCase
{
    public function testSearchLocationWithoutQuery(): void
    {
        self::$client->request('GET', '/quick-search/locations');
        self::assertResponseIsSuccessful();
        $content = $this->getJsonResponse();
        self::assertSame([
            'current_page' => 1,
            'has_previous_page' => false,
            'has_next_page' => true,
            'per_page' => 50,
            'total_items' => 100,
            'total_pages' => 2,
        ], $content['pagination']);

        self::assertCount(50, $content['items']);
    }

    public function testSearchLocationByLocationName(): void
    {
        self::$client->request('GET', '/quick-search/locations?q=location1');

        $location = $this->getTestLocation();

        $this->assertJsonResponseEquals([
            'items' => [
                [
                    'value' => $location->getId(),
                    'label' => $location->getChoiceName(),
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'has_previous_page' => false,
                'has_next_page' => false,
                'per_page' => 50,
                'total_items' => 1,
                'total_pages' => 1,
            ],
        ]);
    }

    public function testSearchCompaniesWithoutQuery(): void
    {
        self::$client->request('GET', '/quick-search/companies');
        self::assertResponseIsSuccessful();
        $content = $this->getJsonResponse();
        self::assertSame([
            'current_page' => 1,
            'has_previous_page' => false,
            'has_next_page' => true,
            'per_page' => 50,
            'total_items' => 101,
            'total_pages' => 3,
        ], $content['pagination']);

        self::assertCount(50, $content['items']);
    }

    public function testSearchByCompanyName(): void
    {
        self::$client->request('GET', '/quick-search/companies?q=test+company');

        $company = $this->getTestCompany();

        $this->assertJsonResponseEquals([
            'items' => [
                [
                    'value' => $company->getId(),
                    'label' => $company->getTradingName(),
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'has_previous_page' => false,
                'has_next_page' => false,
                'per_page' => 50,
                'total_items' => 1,
                'total_pages' => 1,
            ],
        ]);
    }

    public function testItListsPlans(): void
    {
        self::$client->request('GET', '/quick-search/subscriptions');
        $plans = $this->getJsonResponse();

        self::assertCount(3, $plans);
    }
}
