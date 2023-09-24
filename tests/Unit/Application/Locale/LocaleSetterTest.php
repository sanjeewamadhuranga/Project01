<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Locale;

use App\Application\Locale\LocaleNegotiator;
use App\Application\Locale\LocaleSetter;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class LocaleSetterTest extends UnitTestCase
{
    private readonly LocaleNegotiator&MockObject $negotiator;

    private readonly LocaleSetter $localeSetter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->negotiator = $this->createMock(LocaleNegotiator::class);
        $this->localeSetter = new LocaleSetter($this->negotiator);
    }

    public function testItSetsNewPreferredLocaleInSessionAndRequest(): void
    {
        $newLocale = 'vi-VN';
        $request = $this->getRequestWithSession();
        $this->negotiator->expects(self::once())->method('negotiateLocale')->with($newLocale)->willReturn($newLocale);
        $this->localeSetter->setLocale($request, $newLocale);

        self::assertSame($newLocale, $request->getLocale());
        self::assertSame($newLocale, $request->getSession()->get(LocaleSetter::LOCALE_ATTRIBUTE));
    }

    public function testItSetsDefaultLocaleWhenNoneIsSpecified(): void
    {
        $defaultLocale = 'en-GB';
        $request = $this->getRequestWithSession();
        $this->negotiator->expects(self::once())->method('negotiateLocale')->with(null)->willReturn($defaultLocale);
        $this->localeSetter->setLocale($request);

        self::assertSame($defaultLocale, $request->getLocale());
        self::assertSame($defaultLocale, $request->getSession()->get(LocaleSetter::LOCALE_ATTRIBUTE));
    }

    public function testItConvertsShortLocaleFromSessionAndUpdatesIt(): void
    {
        $sessionLocale = 'en';
        $newLocale = 'en-GB';
        $request = $this->getRequestWithSession();
        $request->getSession()->set(LocaleSetter::LOCALE_ATTRIBUTE, $sessionLocale);
        $this->negotiator->expects(self::once())->method('negotiateLocale')->with($sessionLocale)->willReturn($newLocale);
        $this->localeSetter->setLocale($request);

        self::assertSame($newLocale, $request->getLocale());
        self::assertSame($newLocale, $request->getSession()->get(LocaleSetter::LOCALE_ATTRIBUTE));
    }

    public function testItNoopsWhenRequestSessionIsNotValid(): void
    {
        $request = $this->getRequestWithSession();
        // Remove session cookie so hasPreviousSession() returns false
        $request->cookies->remove($request->getSession()->getName());
        $request->getSession()->set(LocaleSetter::LOCALE_ATTRIBUTE, 'en-GB');
        $this->negotiator->expects(self::never())->method('negotiateLocale');
        $this->localeSetter->setLocale($request);

        self::assertSame('en', $request->getLocale());
    }

    private function getRequestWithSession(): Request
    {
        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->cookies->set($session->getName(), null);
        $request->setSession($session);
        $session->start();

        return $request;
    }
}
