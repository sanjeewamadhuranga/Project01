<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\MdrCodeType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;

class MdrCodeTypeTest extends ChoiceTypeTest
{
    private const CODE_1 = 'code1';
    private const CODE_2 = 'code2';
    private const CODE_3 = 'code3';

    private SystemSettings&Stub $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);
        $this->settings->method('getMdrCodes')->willReturn($this->getTestCodes());

        parent::setUp();
    }

    public function testAvailableChoices(array $choices = []): void
    {
        parent::testAvailableChoices([
            new ChoiceView(self::CODE_1, self::CODE_1, self::CODE_1),
            new ChoiceView(self::CODE_2, self::CODE_2, self::CODE_2),
            new ChoiceView(self::CODE_3, self::CODE_3, self::CODE_3),
        ]);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'DEF'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = self::CODE_1): void
    {
        parent::testItSubmitsValidValue($value);
    }

    /**
     * @return string[]
     */
    private function getTestCodes(): array
    {
        return [self::CODE_1, self::CODE_2, self::CODE_3];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new MdrCodeType($this->settings)], []),
        ];
    }

    protected static function getTestedType(): string
    {
        return MdrCodeType::class;
    }
}
