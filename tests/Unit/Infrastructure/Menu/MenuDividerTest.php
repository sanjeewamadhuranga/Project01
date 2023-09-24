<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Menu;

use App\Infrastructure\Menu\MenuDivider;
use App\Tests\Unit\UnitTestCase;

class MenuDividerTest extends UnitTestCase
{
    public function testItIsDividerByDefault(): void
    {
        $divider = MenuDivider::create();

        self::assertTrue($divider->isDivider());
    }
}
