<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Tests\Feature\BaseTestCase;

class NotificationsControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     *
     * @dataProvider notificationTypeProvider
     */
    public function testItListsNotifications(string $type): void
    {
        self::$client->request('GET', sprintf('/notifications/%s/list', $type));
        $this->assertGridResponse();
    }

    /**
     * @return iterable<string, array<int, string>>
     */
    public function notificationTypeProvider(): iterable
    {
        yield 'sms' => ['sms'];
        yield 'email' => ['email'];
        yield 'push' => ['push'];
    }
}
