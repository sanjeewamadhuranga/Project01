<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Locale;

use App\Application\Locale\LocaleNegotiator;
use App\Tests\Unit\UnitTestCase;

class LocaleNegotiatorTest extends UnitTestCase
{
    private const DEFAULT_LOCALE = 'en-GB';
    private const LOCALE_VI = 'vi-VN';

    private readonly LocaleNegotiator $negotiator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->negotiator = new LocaleNegotiator(self::DEFAULT_LOCALE, [self::DEFAULT_LOCALE, self::LOCALE_VI]);
    }

    public function testItReturnsDefaultLocaleWhenNoPreferencePassed(): void
    {
        self::assertSame(self::DEFAULT_LOCALE, $this->negotiator->negotiateLocale(null));
    }

    public function testItReturnsDefaultLocaleForInvalidChoice(): void
    {
        self::assertSame(self::DEFAULT_LOCALE, $this->negotiator->negotiateLocale('pl-PL'));
    }

    public function testItReturnsFullLocaleForPrefix(): void
    {
        self::assertSame(self::DEFAULT_LOCALE, $this->negotiator->negotiateLocale('en'));
        self::assertSame(self::LOCALE_VI, $this->negotiator->negotiateLocale('vi'));
    }

    public function testItReturnsDefaultLocaleForInvalidPrefix(): void
    {
        self::assertSame(self::DEFAULT_LOCALE, $this->negotiator->negotiateLocale('vi-V'));
    }
}
