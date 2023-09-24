<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Twig;

use App\Infrastructure\Twig\RoutingExtension;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RoutingExtensionTest extends UnitTestCase
{
    public function testItRegistersFunctions(): void
    {
        $extension = new RoutingExtension($this->createStub(RequestStack::class));
        $functions = $extension->getFunctions();

        self::assertCount(1, $functions);
        self::assertSame('route_class', $functions[0]->getName());
    }

    public function testItReturnsEmptyStringWhenNoRequest(): void
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects(self::once())->method('getMainRequest')->willReturn(null);

        $extension = new RoutingExtension($requestStack);
        self::assertSame('', $extension->activeRouteClass('routerPrefix'));
    }

    public function testItReturnsActiveStringOnExactRoute(): void
    {
        $extension = $this->getRoutingExtension('some_route');
        self::assertSame('active', $extension->activeRouteClass('some_route'));
    }

    public function testItReturnsActiveStringOnDeeperRoute(): void
    {
        $extension = $this->getRoutingExtension('some_longer_route');
        self::assertSame('active', $extension->activeRouteClass('some'));
    }

    public function testItReturnsDefaultClassOnNotActiveRoute(): void
    {
        $defaultClass = 'myDefaultClass';
        $extension = $this->getRoutingExtension('another_route');
        self::assertSame($defaultClass, $extension->activeRouteClass('some_route', defaultClass: $defaultClass));
    }

    private function getRoutingExtension(string $routeName): RoutingExtension
    {
        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->expects(self::once())->method('get')->with('_route')->willReturn($routeName);

        $request = $this->createMock(Request::class);
        $request->attributes = $parameterBag;

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects(self::once())->method('getMainRequest')->willReturn($request);

        return new RoutingExtension($requestStack);
    }
}
