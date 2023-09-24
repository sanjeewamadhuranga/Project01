<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Domain\Document\Remittance;
use App\Tests\Feature\BaseTestCase;

class RemittanceControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsRemittanceList(): void
    {
        self::$client->request('GET', '/remittances');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('remittance-list');
    }

    /**
     * @group smoke
     */
    public function testItListsRemittance(): void
    {
        self::$client->request('GET', '/remittances/list');

        $this->assertGridResponse();
    }

    /**
     * @group smoke
     */
    public function testItShowsRemittanceDetails(): void
    {
        /** @var Remittance $remittance */
        $remittance = self::$fixtures['remittance_test'];
        self::$client->request('GET', sprintf('/remittances/%s', $remittance->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Pending');
    }
}
