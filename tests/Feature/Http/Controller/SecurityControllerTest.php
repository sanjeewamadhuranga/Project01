<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Tests\Feature\BaseTestCase;

/**
 * @group security
 */
class SecurityControllerTest extends BaseTestCase
{
    protected static bool $authenticate = false;

    public function testUsersCanAuthenticate(): void
    {
        $user = $this->getTestUser();
        self::$client->followRedirects();
        self::$client->request('GET', '/');
        self::assertResponseIsSuccessful();
        self::$client->submitForm('Log in', ['username' => $user->getEmail(), 'password' => 'test']);
        self::assertSelectorTextContains('html', 'Dashboard');
    }

    public function testAuthenticatedUserCannotSeeLoginPage(): void
    {
        $this->authenticate();
        self::$client->followRedirects(false);
        self::$client->request('GET', '/login');
        self::assertResponseRedirects('/');
    }
}
