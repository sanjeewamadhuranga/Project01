<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\HealthCheck;

use App\Domain\Settings\Features;
use App\Infrastructure\HealthCheck\EnableFeatureCheck;
use App\Tests\Unit\UnitTestCase;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use PHPUnit\Framework\MockObject\Stub;

class EnableFeatureCheckTest extends UnitTestCase
{
    private readonly Features&Stub $feature;

    private readonly EnableFeatureCheck $enableFeatureCheck;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feature = $this->createStub(Features::class);

        $this->enableFeatureCheck = new EnableFeatureCheck($this->feature);
    }

    public function testItWillReturnSuccessResultIfThereIsAllNecessaryFeatures(): void
    {
        $this->feature->method('getEnabledFeatures')->willReturn(Features::getConstants());

        self::assertInstanceOf(Success::class, $this->enableFeatureCheck->check());
        self::assertSame('Enabled features check', $this->enableFeatureCheck->getLabel());
    }

    public function testItWillReturnWarningResultIfThereIsOnlySomeFeature(): void
    {
        $this->feature->method('getEnabledFeatures')->willReturn(['INTERCOM_FEATURE' => Features::INTERCOM_FEATURE]);

        $check = $this->enableFeatureCheck->check();
        self::assertInstanceOf(Warning::class, $check);
        self::assertSame('Enabled features check', $this->enableFeatureCheck->getLabel());
        self::assertStringContainsString('features have not set', $check->getMessage());
    }
}
