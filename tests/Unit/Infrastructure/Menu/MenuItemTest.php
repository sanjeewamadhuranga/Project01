<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Menu;

use App\Infrastructure\Menu\MenuItem;
use App\Tests\Unit\UnitTestCase;

class MenuItemTest extends UnitTestCase
{
    public function testItCanHaveChildren(): void
    {
        $item = MenuItem::create();

        self::assertCount(0, $item->getChildren());

        $item->setChildren([MenuItem::create()]);

        self::assertCount(1, $item->getChildren());
    }

    public function testItCanHaveLabel(): void
    {
        $item = MenuItem::create();

        self::assertNull($item->getLabel());

        $item = MenuItem::create(
            label: 'test'
        );

        self::assertSame('test', $item->getLabel());
    }

    public function testItCanHaveRoute(): void
    {
        $item = MenuItem::create();

        self::assertNull($item->getRoute());

        $item = MenuItem::create(
            route: 'test'
        );

        self::assertSame('test', $item->getRoute());
    }

    public function testItCanHaveRouteParams(): void
    {
        $item = MenuItem::create();

        self::assertSame([], $item->getRouteParams());

        $item = MenuItem::create(
            routeParams: ['test' => 'test']
        );

        self::assertSame(['test' => 'test'], $item->getRouteParams());
    }

    public function testItCanHaveRouteClass(): void
    {
        $item = MenuItem::create();

        self::assertNull($item->getRouteClass());

        $item = MenuItem::create(
            routeClass: 'test'
        );

        self::assertSame('test', $item->getRouteClass());
    }

    public function testItCanHaveRouteClassCallback(): void
    {
        $item = MenuItem::create();

        self::assertNull($item->getRouteClassCallback());

        $closure = function (): void {};

        $item = MenuItem::create(
            routeClassCallback: $closure
        );

        self::assertSame($closure, $item->getRouteClassCallback());
    }

    public function testItCanHaveIcon(): void
    {
        $item = MenuItem::create();

        self::assertNull($item->getIcon());

        $item = MenuItem::create(
            icon: 'test'
        );

        self::assertSame('test', $item->getIcon());
    }

    public function testItCanHaveDivider(): void
    {
        $item = MenuItem::create();

        self::assertFalse($item->isDivider());

        $item = MenuItem::create(
            isDivider: true
        );

        self::assertTrue($item->isDivider());
    }
}
