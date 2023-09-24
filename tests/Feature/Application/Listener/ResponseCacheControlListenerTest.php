<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Listener;

use App\Tests\Feature\BaseTestCase;

class ResponseCacheControlListenerTest extends BaseTestCase
{
    public function testResponseHeaderHasNoCache(): void
    {
        self::$client->request('GET', '/');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Cache-Control', 'max-age=0, must-revalidate, no-store, nocache, private');
    }
}
