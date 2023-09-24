<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\HealthCheck;

use App\Domain\Settings\SettingsInterface;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\HealthCheck\ConfigurationCheck;
use App\Tests\Unit\UnitTestCase;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use PHPUnit\Framework\MockObject\Stub;

class ConfigurationCheckTest extends UnitTestCase
{
    private readonly SystemSettings&Stub $systemSettings;

    private readonly ConfigurationCheck $configurationCheck;

    public function setUp(): void
    {
        parent::setUp();
        $this->systemSettings = $this->createStub(SystemSettings::class);
        $this->configurationCheck = new ConfigurationCheck($this->systemSettings);
    }

    public function testItWillReturnFailureResultIfThereIsNoConfigurationSettings(): void
    {
        $this->systemSettings->method('getValue')->willReturn(null);

        $check = $this->configurationCheck->check();
        self::assertInstanceOf(Failure::class, $check);
        self::assertStringContainsString('Missing', $check->getMessage());
        self::assertSame('Check configurations', $this->configurationCheck->getLabel());
    }

    public function testItWillReturnWarningResultIfThereIsAMissingSecondarySettings(): void
    {
        $this->systemSettings
            ->method('getValue')
            ->willReturnCallback(
                static fn (string $key): ?string => SettingsInterface::UNION_PAY_KEY_EXCHANGE === $key ? null : 'test'
            );

        $check = $this->configurationCheck->check();
        self::assertInstanceOf(Warning::class, $check);
        self::assertSame('Missing '.SettingsInterface::UNION_PAY_KEY_EXCHANGE, $check->getMessage());
    }

    public function testItWillReturnSuccessResultIfThereIsConfigurationSettings(): void
    {
        $this->systemSettings->method('getValue')->willReturn('Something');

        self::assertInstanceOf(Success::class, $this->configurationCheck->check());
        self::assertSame('Check configurations', $this->configurationCheck->getLabel());
    }
}
