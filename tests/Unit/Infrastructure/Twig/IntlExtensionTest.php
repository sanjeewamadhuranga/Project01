<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Twig;

use App\Infrastructure\Twig\IntlExtension;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class IntlExtensionTest extends UnitTestCase
{
    public function testItRegistersFunctions(): void
    {
        $extension = new IntlExtension($this->createStub(RequestStack::class));
        $functions = $extension->getFunctions();
        self::assertCount(2, $functions);
        self::assertSame('is_rtl', $functions[0]->getName());
        self::assertSame('intl_direction', $functions[1]->getName());
    }

    public function testItReturnsDirectionForExplicitlySetLocale(): void
    {
        $extension = new IntlExtension($this->createStub(RequestStack::class));

        self::assertFalse($extension->isRtl('dv'));
        self::assertFalse($extension->isRtl('vi'));
        self::assertFalse($extension->isRtl('en'));
        self::assertTrue($extension->isRtl('ar'));
    }

    public function itReturnsScriptDirection(): void
    {
        $extension = new IntlExtension($this->createStub(RequestStack::class));

        self::assertSame('ltr', $extension->direction('dv'));
        self::assertSame('ltr', $extension->direction('vi'));
        self::assertSame('ltr', $extension->direction('en'));
        self::assertSame('rtl', $extension->direction('ar'));
    }

    public function testItReturnsDirectionForRequestLocaleIfItIsNotProvided(): void
    {
        self::assertTrue((new IntlExtension($this->getRequestStack('ar')))->isRtl());
        self::assertFalse((new IntlExtension($this->getRequestStack('vi')))->isRtl());
        self::assertFalse((new IntlExtension($this->getRequestStack('en')))->isRtl());
    }

    private function getRequestStack(string $locale): RequestStack
    {
        $request = $this->createStub(Request::class);
        $request->method('getLocale')->willReturn($locale);
        $requestStack = $this->createStub(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        return $requestStack;
    }
}
