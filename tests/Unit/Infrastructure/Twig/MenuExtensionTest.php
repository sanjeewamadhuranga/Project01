<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Twig;

use App\Infrastructure\Menu\ConfigurationMenu;
use App\Infrastructure\Menu\SideMenu;
use App\Infrastructure\Twig\MenuExtension;
use App\Tests\Unit\UnitTestCase;

class MenuExtensionTest extends UnitTestCase
{
    public function testItRegistersFunctions(): void
    {
        $extension = new MenuExtension(
            [
                'sideMenu' => $this->createStub(SideMenu::class),
                'configMenu' => $this->createStub(ConfigurationMenu::class),
            ]
        );
        $functions = $extension->getFunctions();

        self::assertCount(1, $functions);
        self::assertSame('menu', $functions[0]->getName());
    }
}
