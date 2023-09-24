<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener\Security;

use App\Application\Listener\Security\PasswordExpirationListener;
use App\Domain\Document\Security\Administrator;
use App\Domain\Settings\SystemSettings;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\HttpUtils;

class PasswordExpirationListenerTest extends UnitTestCase
{
    public function testItDoNothingIfPasswordLoginIsDisabled(): void
    {
        $settings = $this->getSystemSettingsWithPasswordLogin(true);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects(self::never())->method('getToken');

        $subscriber = new PasswordExpirationListener($tokenStorage, $this->createStub(HttpUtils::class), $settings);
        $subscriber->__invoke($this->createStub(RequestEvent::class));
    }

    public function testItDoNothingIfPasswordIsNotExpired(): void
    {
        $settings = $this->getSystemSettingsWithPasswordLogin(false);
        $tokenStorage = $this->getTokenStorageWithUserWhosPasswordIsExpired();

        $event = $this->createMock(RequestEvent::class);
        $event->expects(self::never())->method('setResponse');

        $subscriber = new PasswordExpirationListener($tokenStorage, $this->createStub(HttpUtils::class), $settings);
        $subscriber->__invoke($event);
    }

    /**
     * @dataProvider checkRequestPathValues
     */
    public function testItDoNothingOnPasswordChangeRouteAndFosJsRoute(string $route): void
    {
        $settings = $this->getSystemSettingsWithPasswordLogin(false);
        $tokenStorage = $this->getTokenStorageWithUserWhosPasswordIsExpired(true);

        $event = $this->createMock(RequestEvent::class);
        $request = new Request();
        $request->attributes->set('_route', $route);
        $event->method('getRequest')->willReturn($request);
        $event->expects(self::never())->method('setResponse');

        $httpUtils = $this->createStub(HttpUtils::class);

        $subscriber = new PasswordExpirationListener($tokenStorage, $httpUtils, $settings);
        $subscriber->__invoke($event);
    }

    public function testItRedirectsWhenPasswordExpired(): void
    {
        $settings = $this->getSystemSettingsWithPasswordLogin(false);
        $tokenStorage = $this->getTokenStorageWithUserWhosPasswordIsExpired(true);

        $event = $this->createMock(RequestEvent::class);
        $request = new Request();
        $request->attributes->set('_route', 'configuration_settings_index');
        $event->method('getRequest')->willReturn($request);
        $event->expects(self::once())->method('setResponse');

        $httpUtils = $this->createStub(HttpUtils::class);
        $httpUtils->method('createRedirectResponse')->willReturn($this->createStub(RedirectResponse::class));

        $subscriber = new PasswordExpirationListener($tokenStorage, $httpUtils, $settings);
        $subscriber->__invoke($event);
    }

    private function getSystemSettingsWithPasswordLogin(bool $disabled): SystemSettings
    {
        $settings = $this->createStub(SystemSettings::class);
        $settings->method('isPasswordLoginDisabled')->willReturn($disabled);

        return $settings;
    }

    private function getTokenStorageWithUserWhosPasswordIsExpired(bool $expired = false): TokenStorageInterface
    {
        $user = $this->createStub(Administrator::class);
        $user->method('isPasswordExpired')->willReturn($expired);

        $token = $this->createStub(UsernamePasswordToken::class);
        $token->method('getUser')->willReturn($user);

        $tokenStorage = $this->createStub(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        return $tokenStorage;
    }

    /**
     * @return iterable<string, array{string}>
     */
    public function checkRequestPathValues(): iterable
    {
        yield 'PasswordChangeRoute' => ['profile_change_password'];
        yield 'FosJsRoute' => ['fos_js_routing_js'];
    }
}
