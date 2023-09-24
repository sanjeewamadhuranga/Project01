<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener;

use App\Application\Listener\CheckPassportListener;
use App\Domain\Settings\Config;
use App\Domain\Settings\Features;
use App\Domain\Settings\SystemSettings;
use App\Tests\Unit\UnitTestCase;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Debug\TraceableAuthenticator;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckPassportListenerTest extends UnitTestCase
{
    /**
     * @dataProvider getInvalidAuthenticatorCases
     */
    public function testInvalidAuthenticatorUsed(bool $ssoEnabled, bool $passwordEnabled, AuthenticatorInterface $authenticator): void
    {
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->getSubscriber($ssoEnabled, $passwordEnabled)(
            new CheckPassportEvent($authenticator, $this->createStub(Passport::class))
        );
    }

    /**
     * @dataProvider getValidAuthenticatorCases
     */
    public function testValidAuthenticatorUsed(bool $ssoEnabled, bool $passwordEnabled, AuthenticatorInterface $authenticator): void
    {
        $this->getSubscriber($ssoEnabled, $passwordEnabled)(
            new CheckPassportEvent($authenticator, $this->createStub(Passport::class))
        );
        $this->expectNotToPerformAssertions();
    }

    /**
     * @dataProvider getInvalidAuthenticatorCases
     */
    public function testWrappedInvalidAuthenticatorUsed(bool $ssoEnabled, bool $passwordEnabled, AuthenticatorInterface $authenticator): void
    {
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->getSubscriber($ssoEnabled, $passwordEnabled)(
            new CheckPassportEvent(new TraceableAuthenticator($authenticator), $this->createStub(Passport::class))
        );
    }

    /**
     * @dataProvider getValidAuthenticatorCases
     */
    public function testWrappedValidAuthenticatorUsed(bool $ssoEnabled, bool $passwordEnabled, AuthenticatorInterface $authenticator): void
    {
        $this->getSubscriber($ssoEnabled, $passwordEnabled)(
            new CheckPassportEvent(new TraceableAuthenticator($authenticator), $this->createStub(Passport::class))
        );
        $this->expectNotToPerformAssertions();
    }

    /**
     * @return iterable<string, array{bool, bool, AuthenticatorInterface}>
     */
    public function getInvalidAuthenticatorCases(): iterable
    {
        yield 'SSO enabled, password disabled and form login used' => [true, false, $this->createStub(FormLoginAuthenticator::class)];
        yield 'SSO disabled, password enabled and OAuth used' => [false, true, $this->getOAuthAuthenticator()];
        yield 'SSO disabled, password disabled and form login used' => [false, false, $this->createStub(FormLoginAuthenticator::class)];
        yield 'SSO disabled, password disabled and OAuth used' => [false, false, $this->getOAuthAuthenticator()];
    }

    /**
     * @return iterable<string, array{bool, bool, AuthenticatorInterface}>
     */
    public function getValidAuthenticatorCases(): iterable
    {
        yield 'SSO enabled, password disabled and OAuth used' => [true, false, $this->getOAuthAuthenticator()];
        yield 'SSO disabled, password enabled and form login used' => [false, true, $this->createStub(FormLoginAuthenticator::class)];
        yield 'SSO enabled, password enabled and form login used' => [true, true, $this->createStub(FormLoginAuthenticator::class)];
        yield 'SSO enabled, password enabled and OAuth used' => [true, true, $this->getOAuthAuthenticator()];
        yield 'Custom authenticator used' => [false, false, $this->createStub(AuthenticatorInterface::class)];
    }

    private function getSubscriber(bool $ssoEnabled, bool $passwordEnabled): CheckPassportListener
    {
        $features = $this->createStub(Features::class);
        $features->method('isSSOEnabled')->willReturn($ssoEnabled);
        $settings = $this->createStub(SystemSettings::class);
        $settings->method('isPasswordLoginDisabled')->willReturn(!$passwordEnabled);

        $config = $this->createStub(Config::class);
        $config->method('getSettings')->willReturn($settings);
        $config->method('getFeatures')->willReturn($features);

        return new CheckPassportListener($config);
    }

    private function getOAuthAuthenticator(): OAuth2Authenticator
    {
        return $this->createStub(OAuth2Authenticator::class);
    }
}
