<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration\Setting;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Configuration\Settings\LocalizationType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\HttpFoundation\ServerBag;

class LocalizationTypeTest extends BaseSystemSettingTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->request->server = new ServerBag(['REMOTE_ADDR' => 'test']);
    }

    public function testItHasCorrectDefaultValues(): void
    {
        $form = $this->factory->create(LocalizationType::class);

        self::assertSame([
            ['default' => true, 'value' => 'Europe/London'],
            ['default' => false, 'value' => 'Asia/Kolkata'],
            ['default' => false, 'value' => 'Asia/Ho_Chi_Minh'],
            ['default' => false, 'value' => 'Asia/Singapore'],
        ], $form->get(SystemSettings::ENABLED_TIMEZONES)->getData());

        self::assertSame([
            ['default' => true, 'value' => 'GBP'],
            ['default' => false, 'value' => 'EUR'],
            ['default' => false, 'value' => 'USD'],
            ['default' => false, 'value' => 'SGD'],
            ['default' => false, 'value' => 'AED'],
            ['default' => false, 'value' => 'HKD'],
            ['default' => false, 'value' => 'CHF'],
            ['default' => false, 'value' => 'AUD'],
        ], $form->get(SystemSettings::ENABLED_CURRENCIES)->getData());

        self::assertSame([
            ['key' => 'default', 'value' => 'GB'],
            ['key' => 'GB', 'value' => 'United Kingdom'],
            ['key' => 'BE', 'value' => 'Belgium'],
            ['key' => 'FR', 'value' => 'France'],
            ['key' => 'NL', 'value' => 'The Netherlands'],
            ['key' => 'VN', 'value' => 'Vietnam'],
            ['key' => 'SG', 'value' => 'Singapore'],
            ['key' => 'LK', 'value' => 'Sri Lanka'],
        ], $form->get(SystemSettings::ENABLED_COUNTRIES)->getData());

        self::assertSame([
            ['key' => 'default', 'value' => 'EN'],
            ['key' => 'EN', 'value' => 'English'],
            ['key' => 'VI', 'value' => 'Vietnamese'],
        ], $form->get(SystemSettings::ENABLED_LANGUAGES)->getData());
    }

    /**
     * @return array<int, PreloadedExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new LocalizationType($this->settings, $this->requestStack)], []),
        ];
    }
}
