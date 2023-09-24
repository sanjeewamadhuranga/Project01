<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\TimezoneType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;

class TimezoneTypeTest extends ChoiceTypeTest
{
    private const TIMEZONE_LONDON = 'Europe/London';
    private const TIMEZONE_SINGAPORE = 'Asia/Singapore';

    private SystemSettings&Stub $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);
        $this->settings->method('getEnabledTimezones')->willReturn($this->getSampleTimezones());

        parent::setUp();
    }

    public function testAvailableChoices(array $choices = []): void
    {
        parent::testAvailableChoices([
            new ChoiceView(self::TIMEZONE_LONDON, self::TIMEZONE_LONDON, self::TIMEZONE_LONDON),
            new ChoiceView(self::TIMEZONE_SINGAPORE, self::TIMEZONE_SINGAPORE, self::TIMEZONE_SINGAPORE),
        ]);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'Asia/Kolkata'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = self::TIMEZONE_LONDON): void
    {
        parent::testItSubmitsValidValue($value);
    }

    /**
     * @return array<string, string>
     */
    protected function getSampleTimezones(): array
    {
        return [
            self::TIMEZONE_LONDON => self::TIMEZONE_LONDON,
            self::TIMEZONE_SINGAPORE => self::TIMEZONE_SINGAPORE,
        ];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new TimezoneType($this->settings)], []),
        ];
    }

    protected static function getTestedType(): string
    {
        return TimezoneType::class;
    }
}
