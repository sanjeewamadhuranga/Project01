<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Tests\Feature\BaseTestCase;

class DashboardControllerTest extends BaseTestCase
{
    protected static bool $authenticate = false;

    public function testAnonymousUsersCannotSeeDashboard(): void
    {
        self::$client->request('GET', '/');
        self::assertResponseRedirects('http://localhost/login');
    }
}
