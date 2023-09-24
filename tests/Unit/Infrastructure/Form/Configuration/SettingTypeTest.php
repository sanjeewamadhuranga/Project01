<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration;

use App\Application\Settings\Type;
use App\Domain\Document\Setting;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Configuration\SettingType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class SettingTypeTest extends TypeTestCase
{
    private SystemSettings&Stub $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);

        parent::setUp();
    }

    public function testItShowsAllSettingNamesIfThereAreNoSettingsInDatabase(): void
    {
        $this->settings->method('getAll')->willReturn([]);
        $form = $this->factory->create(SettingType::class);
        $choices = $this->getNameChoices($form);

        $settingNames = SystemSettings::getConstants();
        sort($settingNames);

        self::assertSame(array_combine($settingNames, $settingNames), $choices);
        self::assertContains(SystemSettings::ADMIN_THEME, $choices);
        self::assertContains(SystemSettings::OWNER, $choices);
        self::assertContains(SystemSettings::MDR_CODES, $choices);
        self::assertNotEmpty($choices);
    }

    public function testItShowsOnlyRemainingSettingNames(): void
    {
        $existingSettings = [
            SystemSettings::ADMIN_THEME => new Setting(SystemSettings::ADMIN_THEME),
            SystemSettings::OWNER => new Setting(SystemSettings::OWNER),
            SystemSettings::MDR_CODES => new Setting(SystemSettings::MDR_CODES),
        ];
        $this->settings->method('getAll')->willReturn($existingSettings);
        $form = $this->factory->create(SettingType::class);
        $choices = $this->getNameChoices($form);

        self::assertCount(count(SystemSettings::getConstants()) - count($existingSettings), $choices);
        foreach ($existingSettings as $settingKey => $setting) {
            self::assertNotContains($settingKey, $choices);
        }
    }

    public function testPlainTypeSavesValueAsIs(): void
    {
        $form = $this->factory->create(SettingType::class, null, ['type' => Type::PLAIN])
            ->submit([
                'name' => SystemSettings::ADMIN_THEME,
                'value' => 'pay',
            ]);

        $setting = $form->getData();
        self::assertInstanceOf(Setting::class, $setting);
        self::assertSame('ADMIN_THEME', $setting->getName());
        self::assertSame('pay', $setting->getValue());
    }

    public function testItAllowsCreatingCollectionValue(): void
    {
        $form = $this->factory->create(SettingType::class, null, ['type' => Type::COLLECTION])
            ->submit([
                'name' => SystemSettings::ENABLED_ACCOUNT_TYPES,
                'value' => ['iban', 'custom'],
            ]);

        $setting = $form->getData();
        self::assertInstanceOf(Setting::class, $setting);
        self::assertSame(SystemSettings::ENABLED_ACCOUNT_TYPES, $setting->getName());
        self::assertSame(['iban', 'custom'], $setting->getValue());
    }

    public function testItAllowsEditingCollectionValue(): void
    {
        $setting = new Setting(SystemSettings::ENABLED_ACCOUNT_TYPES);
        $setting->setValue(['iban', 'custom']);
        $form = $this->factory->create(SettingType::class, $setting, ['type' => Type::COLLECTION])
            ->submit([
                'name' => SystemSettings::ENABLED_ACCOUNT_TYPES,
                'value' => ['account', 'test'],
            ]);

        $setting = $form->getData();
        self::assertInstanceOf(Setting::class, $setting);
        self::assertSame(SystemSettings::ENABLED_ACCOUNT_TYPES, $setting->getName());
        self::assertSame(['account', 'test'], $setting->getValue());
    }

    public function testItConvertsSubmittedCollectionDataToList(): void
    {
        $setting = new Setting(SystemSettings::ENABLED_ACCOUNT_TYPES);
        $setting->setValue(['iban', 'custom']);
        $form = $this->factory->create(SettingType::class, $setting, ['type' => Type::COLLECTION])
            ->submit([
                'name' => SystemSettings::ENABLED_ACCOUNT_TYPES,
                'value' => [1 => 'account', 3 => 'test'],
            ]);

        $setting = $form->getData();
        self::assertInstanceOf(Setting::class, $setting);
        self::assertSame(SystemSettings::ENABLED_ACCOUNT_TYPES, $setting->getName());
        self::assertSame(['account', 'test'], $setting->getValue());
    }

    public function testItAllowsCreatingObjectValues(): void
    {
        $form = $this->factory->create(SettingType::class, null, ['type' => Type::OBJECT])
            ->submit([
                'name' => SystemSettings::CURRENCY_LIMITS,
                'value' => [
                    ['key' => 'EUR_MIN', 'value' => '40'],
                    ['key' => 'EUR_MAX', 'value' => '15000000'],
                ],
            ]);

        $setting = $form->getData();
        self::assertInstanceOf(Setting::class, $setting);
        self::assertSame(SystemSettings::CURRENCY_LIMITS, $setting->getName());
        self::assertSame(['EUR_MIN' => '40', 'EUR_MAX' => '15000000'], $setting->getValue());
    }

    public function testItAllowsEditingObjectValues(): void
    {
        $setting = new Setting(SystemSettings::CURRENCY_LIMITS);
        $setting->setValue([
            'EUR_MIN' => '40',
            'EUR_MAX' => '15000000',
        ]);
        $form = $this->factory->create(SettingType::class, $setting, ['type' => Type::OBJECT]);

        self::assertSame([
            ['key' => 'EUR_MIN', 'value' => '40'],
            ['key' => 'EUR_MAX', 'value' => '15000000'],
        ], $form->get('value')->getNormData());

        $form->submit([
            'name' => SystemSettings::CURRENCY_LIMITS,
            'value' => [
                ['key' => 'USD_MIN', 'value' => '10'],
                ['key' => 'USD_MAX', 'value' => '50'],
            ],
        ]);

        $setting = $form->getData();
        self::assertInstanceOf(Setting::class, $setting);
        self::assertSame(SystemSettings::CURRENCY_LIMITS, $setting->getName());
        self::assertSame(['USD_MIN' => '10', 'USD_MAX' => '50'], $setting->getValue());
    }

    /**
     * @dataProvider differentTypeValueDataProvider
     */
    public function testItDoNotDisplayMaskedValue(mixed $value, object $type, mixed $expected): void
    {
        $setting = new Setting(SystemSettings::CURRENCY_LIMITS);
        $setting->setValue($value);
        $setting->setMaskValue(true);
        $form = $this->factory->create(SettingType::class, $setting, ['type' => $type]);

        self::assertSame($expected, $form->get('value')->getViewData());
        self::assertSame($expected, $form->get('value')->getNormData());
        self::assertSame($expected, $form->get('value')->getData());
    }

    /**
     * @dataProvider differentTypeValueDataProvider
     */
    public function testItDisplaysNotMaskedValue(mixed $value, object $type): void
    {
        $setting = new Setting(SystemSettings::CURRENCY_LIMITS);
        $setting->setValue($value);
        $form = $this->factory->create(SettingType::class, $setting, ['type' => $type]);

        if (Type::OBJECT === $type) {
            $expectedValue = [];
            foreach ($value as $key => $item) {
                $expectedValue[] = ['key' => $key, 'value' => $item];
            }
            $value = $expectedValue;
        }

        self::assertSame($value, $form->get('value')->getViewData());
        self::assertSame($value, $form->get('value')->getNormData());
        self::assertSame($value, $form->get('value')->getData());
    }

    public function testItIsTransformingTheBooleanValueToString(): void
    {
        $form = $this->factory->create(SettingType::class, null, ['type' => Type::BOOL])->submit([
            'name' => SystemSettings::COUNTRIES,
            'value' => true,
        ]);

        $setting = $form->getData();
        self::assertInstanceOf(Setting::class, $setting);
        self::assertSame('COUNTRIES', $setting->getName());
        self::assertSame('true', $setting->getValue());

        $form = $this->factory->create(SettingType::class, null, ['type' => Type::BOOL])->submit([
            'name' => SystemSettings::COUNTRIES,
            'value' => false,
        ]);

        $setting = $form->getData();
        self::assertInstanceOf(Setting::class, $setting);
        self::assertSame('COUNTRIES', $setting->getName());
        self::assertSame('false', $setting->getValue());
    }

    public function testItNoMaskValueOptionWhenTypeIsBool(): void
    {
        $setting = new Setting(SystemSettings::COUNTRIES);
        $form = $this->factory->create(SettingType::class, $setting, ['type' => Type::BOOL]);
        self::assertFalse($form->offsetExists('maskValue'));
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new SettingType($this->settings)], []),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getNameChoices(FormInterface $form): array
    {
        $choices = [];

        /** @var ChoiceView $choice */
        foreach ($form->get('name')->createView()->vars['choices'] ?? [] as $choice) {
            $choices[(string) $choice->value] = (string) $choice->label;
        }

        return $choices;
    }

    /**
     * @return iterable<string, array{mixed, object, mixed}>
     */
    public function differentTypeValueDataProvider(): iterable
    {
        yield 'string' => ['someValue', Type::PLAIN, ''];
        yield 'bool as string' => ['false', Type::PLAIN, ''];
        yield 'array' => [['aaa', 'bbb', 'ccc'], Type::COLLECTION, ['', '', '']];
        yield 'object' => [
            ['attribute1' => 'value1', 'attribute2' => 'value2', 'attribute3' => 'value3'],
            Type::OBJECT,
            [
                [
                    'key' => 'attribute1',
                    'value' => '',
                ],
                [
                    'key' => 'attribute2',
                    'value' => '',
                ],
                [
                    'key' => 'attribute3',
                    'value' => '',
                ],
            ],
        ];
    }
}
