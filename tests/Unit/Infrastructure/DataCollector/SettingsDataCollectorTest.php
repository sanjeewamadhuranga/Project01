<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataCollector;

use App\Domain\Document\Setting;
use App\Domain\Settings\Branding;
use App\Domain\Settings\Config;
use App\Domain\Settings\Features;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\DataCollector\SettingsDataCollector;
use App\Tests\Unit\UnitTestCase;
use ReflectionClass;

class SettingsDataCollectorTest extends UnitTestCase
{
    public function testItSetsDataOnLateCollect(): void
    {
        $setting1Value = 'setting1Value';
        $setting2Value = ['setting2Value' => 'abc'];
        $setting1 = $this->createStub(Setting::class);
        $setting1->method('getValue')->willReturn($setting1Value);
        $setting2 = $this->createStub(Setting::class);
        $setting2->method('getValue')->willReturn($setting2Value);

        $enabledFeatures = ['enabledFeature1', 'enabledFeature2'];
        $constants = ['constant'];
        $allSettings = ['firstSettingName' => $setting1, 'secondSettingName' => $setting2];
        $owner = 'owner';
        $theme = 'theme';

        $config = $this->createStub(Config::class);
        $config->method('getBranding')->willReturn($this->getBranding($owner, $theme));
        $config->method('getFeatures')->willReturn($this->getFeatures($enabledFeatures, $constants));
        $config->method('getSettings')->willReturn($this->getSystemSettings($allSettings));

        $settingsDataCollector = new SettingsDataCollector($config);
        $settingsDataCollector->lateCollect();

        self::assertSame($owner, $settingsDataCollector->getOwner());
        self::assertSame($theme, $settingsDataCollector->getTheme());
        self::assertSame(['firstSettingName' => $setting1Value, 'secondSettingName' => $setting2Value], $settingsDataCollector->getSettings());
        self::assertSame([
            'enabled' => $enabledFeatures,
            'all' => Features::getConstants(),
        ], $settingsDataCollector->getFeatures());
    }

    public function testItResetsData(): void
    {
        $setting1Value = 'setting1ValueAnotherTest';
        $setting1 = $this->createStub(Setting::class);
        $setting1->method('getValue')->willReturn($setting1Value);

        $enabledFeatures = ['enabledFeature111'];
        $constants = ['someConstant'];
        $allSettings = ['someSetting' => $setting1];
        $owner = 'anotherOwner';
        $theme = 'betterTheme';

        $config = $this->createStub(Config::class);
        $config->method('getBranding')->willReturn($this->getBranding($owner, $theme));
        $config->method('getFeatures')->willReturn($this->getFeatures($enabledFeatures, $constants));
        $config->method('getSettings')->willReturn($this->getSystemSettings($allSettings));

        $settingsDataCollector = new SettingsDataCollector($config);
        $settingsDataCollector->lateCollect();

        $reflectionClass = new ReflectionClass(SettingsDataCollector::class);
        $reflectionProperty = $reflectionClass->getProperty('data');
        $reflectionProperty->setAccessible(true);

        self::assertNotEmpty($reflectionProperty->getValue($settingsDataCollector));

        $settingsDataCollector->reset();

        self::assertSame([], $reflectionProperty->getValue($settingsDataCollector));
    }

    public function testItReturnsTemplatePath(): void
    {
        self::assertSame('data_collector/settings.html.twig', SettingsDataCollector::getTemplate());
    }

    private function getBranding(string $owner, string $theme): Branding
    {
        $systemSettings = $this->createStub(SystemSettings::class);
        $systemSettings->method('getValue')->willReturnOnConsecutiveCalls($owner, $theme);

        return new Branding($systemSettings);
    }

    /**
     * @param array<int, string> $enabledFeatures
     * @param array<int, string> $constants
     */
    private function getFeatures(array $enabledFeatures, array $constants): Features
    {
        $systemSettings = $this->createStub(SystemSettings::class);
        $systemSettings->method('getValue')->willReturnOnConsecutiveCalls($enabledFeatures, $constants);

        return new Features($systemSettings);
    }

    /**
     * @param array<string, Setting> $allSettings
     */
    private function getSystemSettings(array $allSettings): SystemSettings
    {
        $systemSettings = $this->createStub(SystemSettings::class);
        $systemSettings->method('getAll')->willReturn($allSettings);

        return $systemSettings;
    }
}
