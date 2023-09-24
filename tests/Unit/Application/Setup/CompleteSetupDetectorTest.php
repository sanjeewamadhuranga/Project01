<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Setup;

use App\Application\Setup\CompleteSetupDetector;
use App\Domain\Settings\SystemSettings;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\Stub;

class CompleteSetupDetectorTest extends UnitTestCase
{
    private SystemSettings&Stub $settings;

    private CompleteSetupDetector $detector;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);

        $this->detector = new CompleteSetupDetector($this->settings);
    }

    /**
     * @dataProvider systemSettingsValuesProvider
     *
     * @param array<int, true|null> $values
     */
    public function testItReturnsFalseWhenOneOfTheNeededOptionIsNotSet(array $values): void
    {
        $this->settings->method('getValue')->willReturnOnConsecutiveCalls(...$values);

        self::assertFalse($this->detector->isCompleted());
    }

    public function testItReturnsFalsWhenAllRequiredOptionsAreSet(): void
    {
        $this->settings->method('getValue')->willReturnOnConsecutiveCalls(true, true, true, true, true, true, true, true, true, true, true);

        self::assertTrue($this->detector->isCompleted());
    }

    /**
     * @return iterable<string, array<int, array<int, bool|null>>>
     */
    public function systemSettingsValuesProvider(): iterable
    {
        yield 'FEDERATED_ID_PRIMARY is not set' => [[true, true, true, true, true, true, true, true, true, true, null]];
        yield 'ENABLED_FEATURES is not set' => [[true, true, true, true, true, true, true, true, true, null, true]];
        yield 'ENABLED_LANGUAGES is not set' => [[true, true, true, true, true, true, true, true, null, true, true]];
        yield 'ENABLED_COUNTRIES is not set' => [[true, true, true, true, true, true, true, null, true, true, true]];
        yield 'ENABLED_CURRENCIES is not set' => [[true, true, true, true, true, true, null, true, true, true, true]];
        yield 'ENABLED_TIMEZONES is not set' => [[true, true, true, true, true, null, true, true, true, true, true]];
        yield 'MANAGER_PORTAL_URL is not set' => [[true, true, true, true, null, true, true, true, true, true, true]];
        yield 'API_DOMAIN is not set' => [[true, true, true, null, true, true, true, true, true, true, true]];
        yield 'DASHBOARD is not set' => [[true, true, null, true, true, true, true, true, true, true, true]];
        yield 'ADMIN_THEME is not set' => [[true, null, true, true, true, true, true, true, true, true, true]];
        yield 'OWNER is not set' => [[null, true, true, true, true, true, true, true, true, true, true]];
    }
}
