<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Tests\Feature\BaseTestCase;

/**
 * @group smoke
 */
class StatsControllerTest extends BaseTestCase
{
    public function testItProvidesMerchantOverview(): void
    {
        self::$client->request('GET', '/stats/merchants/overview');
        self::assertResponseIsSuccessful();
        $data = $this->getJsonResponse();
        self::assertSame(41, $data['confirmedCount']);
        self::assertSame(25, $data['pendingCount']);
        self::assertSame(81, $data['totalCount']);
        self::assertSame(1, $data['pendingPayouts']);
    }

    public function testItProvidesTransactionTotals(): void
    {
        self::$client->request('GET', '/stats/transaction-totals/GBP');
        self::assertResponseIsSuccessful();
        $data = $this->getJsonResponse();
        self::assertGreaterThanOrEqual(365, is_countable($data) ? count($data) : 0);
        self::assertIsInt($data[0]['amount']);
    }
}
