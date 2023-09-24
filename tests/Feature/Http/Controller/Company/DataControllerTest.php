<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Tests\Feature\BaseTestCase;

class DataControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsCompanyData(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/data', $this->getTestCompany()->getId()));

        self::assertResponseIsSuccessful();
    }
}
