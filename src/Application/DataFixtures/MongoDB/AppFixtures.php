<?php

declare(strict_types=1);

namespace App\Application\DataFixtures\MongoDB;

use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\Loader\PurgerLoader;

class AppFixtures extends Fixture
{
    public function __construct(private readonly PurgerLoader $loader)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loader->load([
            'fixtures/user.yml',
            'fixtures/setting.yml',
            'fixtures/transaction.yml',
            'fixtures/company.yml',
            'fixtures/subscription_plan.yml',
        ]);
    }
}
