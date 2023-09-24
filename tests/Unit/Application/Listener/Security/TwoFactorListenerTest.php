<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener\Security;

use App\Application\Listener\Security\TwoFactorEnforceListener;
use App\Domain\Document\Security\Administrator;
use App\Domain\Settings\Features;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\HttpUtils;

class TwoFactorListenerTest extends UnitTestCase
{
    private TokenStorageInterface&MockObject $tokenStorage;

    private HttpUtils&MockObject $httpUtils;

    private Features&MockObject $features;

    private TwoFactorEnforceListener $twoFactorSubscriber;

    private RequestEvent&MockObject $requestEvent;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);

        $this->httpUtils = $this->createMock(HttpUtils::class);

        $this->features = $this->createMock(Features::class);

        $this->twoFactorSubscriber = new TwoFactorEnforceListener($this->tokenStorage, $this->httpUtils, $this->features);

        $this->requestEvent = $this->createMock(RequestEvent::class);
    }

    public function testItDoNothingWhen2FaIsNotEnforced(): void
    {
        $this->features->method('is2FAEnforced')->willReturn(false);

        $this->requestEvent->expects(self::never())->method('setResponse');

        $this->twoFactorSubscriber->__invoke($this->requestEvent);
    }

    public function testItDoNothingWhen2FaIsNotEnabled(): void
    {
        $this->features->method('is2FAEnforced')->willReturn(true);
        $this->features->method('is2FAEnabled')->willReturn(false);

        $this->requestEvent->expects(self::never())->method('setResponse');

        $this->twoFactorSubscriber->__invoke($this->requestEvent);
    }

    public function testItDoNothingWhenTokenIsNotUsernamePasswordTokenOrRememberMeToken(): void
    {
        $this->features->method('is2FAEnforced')->willReturn(true);
        $this->features->method('is2FAEnabled')->willReturn(true);
        $this->tokenStorage->method('getToken')->willReturn($this->createStub(TokenInterface::class));

        $this->requestEvent->expects(self::never())->method('setResponse');

        $this->twoFactorSubscriber->__invoke($this->requestEvent);
    }

    public function testItDoNothingWhenUserIsNotSecurityUser(): void
    {
        $this->features->method('is2FAEnforced')->willReturn(true);
        $this->features->method('is2FAEnabled')->willReturn(true);

        $token = $this->createStub(UsernamePasswordToken::class);
        $token->method('getUser')->willReturn($this->createStub(UserInterface::class));
        $this->tokenStorage->method('getToken')->willReturn($token);

        $this->requestEvent->expects(self::never())->method('setResponse');

        $this->twoFactorSubscriber->__invoke($this->requestEvent);
    }

    public function testItDoNothingWhenUserHave2FaEnabled(): void
    {
        $this->features->method('is2FAEnforced')->willReturn(true);
        $this->features->method('is2FAEnabled')->willReturn(true);
        $this->tokenStorage->method('getToken')->willReturn($this->getTokenWithUser(true));

        $this->requestEvent->expects(self::never())->method('setResponse');

        $this->twoFactorSubscriber->__invoke($this->requestEvent);
    }

    /**
     * @dataProvider allowedRouteProvider
     */
    public function testItDoNotRedirectsOnAllowedRoute(string $route): void
    {
        $this->features->method('is2FAEnforced')->willReturn(true);
        $this->features->method('is2FAEnabled')->willReturn(true);
        $this->tokenStorage->method('getToken')->willReturn($this->getTokenWithUser());

        $this->requestEvent->method('getRequest')->willReturn($this->getRequest($route));
        $this->requestEvent->expects(self::never())->method('setResponse');

        $this->twoFactorSubscriber->__invoke($this->requestEvent);
    }

    /**
     * @dataProvider notAllowedRouteProvider
     */
    public function testItRedirectsOnNotAllowedRoute(string $route): void
    {
        $this->features->method('is2FAEnforced')->willReturn(true);
        $this->features->method('is2FAEnabled')->willReturn(true);
        $this->tokenStorage->method('getToken')->willReturn($this->getTokenWithUser());

        $request = $this->getRequest($route);
        $redirectResponse = $this->createStub(RedirectResponse::class);
        $this->httpUtils->method('createRedirectResponse')->with($request, 'profile_index')->willReturn($redirectResponse);

        $this->requestEvent->method('getRequest')->willReturn($request);
        $this->requestEvent->expects(self::once())->method('setResponse')->with($redirectResponse);

        $this->twoFactorSubscriber->__invoke($this->requestEvent);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public function allowedRouteProvider(): iterable
    {
        yield 'logout' => ['logout'];
        yield '2fa_login' => ['2fa_login'];
        yield '2fa_login_check' => ['2fa_login_check'];
        yield 'fos_js_routing_js' => ['fos_js_routing_js'];
        yield 'profile_index' => ['profile_index'];
        yield 'profile_2fa_get_app' => ['profile_2fa_get_app'];
        yield 'profile_2fa_enable_app' => ['profile_2fa_enable_app'];
        yield 'profile_2fa_get_sms' => ['profile_2fa_get_sms'];
        yield 'profile_2fa_enable_sms' => ['profile_2fa_enable_sms'];
        yield 'profile_2fa_security' => ['profile_2fa_security'];
        yield 'profile_2fa_disable' => ['profile_2fa_disable'];
        yield 'profile_change_password' => ['profile_change_password'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public function notAllowedRouteProvider(): iterable
    {
        yield 'administrators_index' => ['administrators_index'];
        yield 'administrators_create' => ['administrators_create'];
        yield 'merchants_index' => ['merchants_index'];
        yield 'configuration_roles_index' => ['configuration_roles_index'];
        yield 'configuration_settings_index' => ['configuration_settings_index'];
        yield 'transaction_index' => ['transaction_index'];
        yield 'search' => ['search'];
    }

    private function getTokenWithUser(bool $is2FaEnabled = false): UsernamePasswordToken
    {
        $user = $this->createStub(Administrator::class);
        $user->method('is2FaEnabled')->willReturn($is2FaEnabled);

        $token = $this->createStub(UsernamePasswordToken::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }

    private function getRequest(string $requestedRoute): Request
    {
        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->method('get')->with('_route')->willReturn($requestedRoute);

        $request = new Request();
        $request->attributes = $parameterBag;

        return $request;
    }
}
