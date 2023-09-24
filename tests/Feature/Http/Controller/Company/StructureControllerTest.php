<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Tests\Feature\BaseTestCase;

class StructureControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsStructurePage(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/structure', $this->getTestCompany()->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('company-directors-list');
        self::assertSelectorExists('company-shareholders-list');
    }

    /**
     * @group smoke
     */
    public function testItListsDirectors(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/structure/directors/list', $this->getTestCompany()->getId()));

        $this->assertGridResponse();
    }

    /**
     * @group smoke
     */
    public function testItListsShareholders(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/structure/shareholders/list', $this->getTestCompany()->getId()));

        $this->assertGridResponse();
    }
}
