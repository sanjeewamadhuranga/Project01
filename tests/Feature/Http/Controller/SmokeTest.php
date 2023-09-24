<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Tests\Feature\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Tests all available GET routes that do not require any parameters.
 *
 * @group smoke
 */
class SmokeTest extends BaseTestCase
{
    protected static bool $loadFixtures = false;

    protected static bool $authenticate = false;

    private const EXCLUDED_ROUTES = [
        'onboarding_federated_identity_list', // Requires mocking Cognito client
        'administrators_export', // Produces output, tested elsewhere
        'profile_2fa_disable', // Requires 2FA to be enabled
        'connect_google', // Requires Google client ID
        '2fa_login', // Requires 2FA to be enabled
        '2fa_login_check', // Requires 2FA to be enabled
        'google_login', // Requires a valid Google token
        'login', // Produces a redirect for authenticated users - tested elsewhere
        'logout', // Produces a redirect after logout - tested elsewhere
        'search', // Needs at least 3 characters in query
        'profile_2fa_disable_sms', // Produces a redirect response
        'profile_2fa_disable_app', // Produces a redirect response
        '2fa_resend_sms', // Produces a redirect response
        'setup_accounts', // Produces a redirect response
        'setup_branding', // Produces a redirect response
        'setup_currencies', // Produces a redirect response
        'setup_features', // Produces a redirect response
        'setup_federated_identity', // Produces a redirect response
    ];

    /**
     * @dataProvider allGetRoutesProvider
     *
     * @param array<string, mixed> $parameters
     */
    public function testResponseIsSuccessful(string $route, array $parameters = [], string $method = Request::METHOD_GET): void
    {
        $this->authenticate();
        self::$client->request($method, self::getContainer()->get(RouterInterface::class)->generate($route, $parameters));
        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public function allGetRoutesProvider(): iterable
    {
        $this->initialize();
        $router = self::getContainer()->get(RouterInterface::class);

        foreach ($router->getRouteCollection()->all() as $routeName => $route) {
            if (in_array($routeName, self::EXCLUDED_ROUTES, true)) {
                continue;
            }

            if ([] !== $route->getMethods() && !in_array(Request::METHOD_GET, $route->getMethods(), true)) {
                continue;
            }

            if ($this->requiresParameters($route)) {
                continue;
            }

            yield $routeName => [$routeName];
        }

        return [];
    }

    private function initialize(): void
    {
        $this->setUp();
        $this->loadFixtures();
    }

    private function requiresParameters(Route $route): bool
    {
        foreach ($route->compile()->getVariables() as $variable) {
            if (null === $route->getDefault($variable)) {
                return true;
            }
        }

        return false;
    }
}
