<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Fidry\AliceDataFixtures\LoaderInterface;

final class Fixtures
{
    /** @var array<string, object> */
    public static array $fixtures;

    public static LoaderInterface $loader;

    /**
     * @return object[]
     */
    public static function loadFixtures(bool $force = false): array
    {
        if (!isset(self::$fixtures) || $force) {
            self::$fixtures = self::$loader->load([
                'fixtures/user.yml',
                'fixtures/setting.yml',
                'fixtures/transaction.yml',
                'fixtures/company.yml',
                'fixtures/subscription_plan.yml',
                'fixtures/dispute.yml',
                'fixtures/role.yml',
                'fixtures/invitation.yml',
                'fixtures/registration.yml',
                'fixtures/remittance.yml',
                'fixtures/risk_profile.yml',
                'fixtures/config.yml',
                'fixtures/discountCode.yml',
                'fixtures/location.yml',
                'fixtures/terms.yml',
                'fixtures/compliance_case.yml',
            ]);
        }

        return self::$fixtures;
    }
}
