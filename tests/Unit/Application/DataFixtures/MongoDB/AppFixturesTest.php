<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\DataFixtures\MongoDB;

use App\Application\DataFixtures\MongoDB\AppFixtures;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\Loader\PurgerLoader;

class AppFixturesTest extends UnitTestCase
{
    public function testItLoadsFixtures(): void
    {
        $loader = $this->createMock(PurgerLoader::class);
        $loader->expects(self::once())->method('load')->with([
            'fixtures/user.yml',
            'fixtures/setting.yml',
            'fixtures/transaction.yml',
            'fixtures/company.yml',
            'fixtures/subscription_plan.yml',
        ]);

        $fixtures = new AppFixtures($loader);
        $fixtures->load($this->createStub(ObjectManager::class));
    }
}
