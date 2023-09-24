<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Tests\Feature\BaseTestCase;

class ActivityControllerTest extends BaseTestCase
{
    public function testItShowsIndexOfActivityPage(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s/activity/notes', $testCompany->getId()));
        self::assertResponseIsSuccessful();
    }
}
