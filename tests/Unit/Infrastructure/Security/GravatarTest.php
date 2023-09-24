<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use App\Infrastructure\Security\Gravatar;
use App\Tests\Unit\UnitTestCase;

class GravatarTest extends UnitTestCase
{
    public function testItGeneratesAvatarUrl(): void
    {
        self::assertSame(
            'https://secure.gravatar.com/avatar/55502f40dc8b7c769880b10874abc9d0',
            Gravatar::getAvatar('test@EXAMPLE.com')
        );
    }
}
