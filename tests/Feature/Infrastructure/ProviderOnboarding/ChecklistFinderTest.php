<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\ProviderOnboarding;

use App\Domain\Transaction\Provider;
use App\Infrastructure\ProviderOnboarding\ChecklistFinder;
use App\Tests\Feature\BaseTestCase;

/**
 * @testdox Make sure all payment providers have an onboarding checklist assigned
 */
class ChecklistFinderTest extends BaseTestCase
{
    protected static bool $loadFixtures = false;

    /**
     * @dataProvider paymentProviders
     */
    public function testChecklistExistsForProvider(string $provider): void
    {
        $checklist = self::$client->getContainer()->get(ChecklistFinder::class)->find($provider);
        self::assertTrue($checklist->isApplicable($provider)); // Dummy check just to make sure no exception was thrown and hit the coverage.
    }

    /**
     * @return iterable<string, array{string}>
     */
    public function paymentProviders(): iterable
    {
        foreach (Provider::cases() as $provider) {
            yield $provider->value => [$provider->value];
        }
    }
}
