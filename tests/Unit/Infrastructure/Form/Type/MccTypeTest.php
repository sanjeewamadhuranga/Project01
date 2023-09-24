<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Domain\Settings\SettingsInterface;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\MccType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;

class MccTypeTest extends ChoiceTypeTest
{
    private readonly SystemSettings&MockObject $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createMock(SystemSettings::class);

        parent::setUp();
    }

    public function testAvailableChoices(array $choices = []): void
    {
        $form = $this->factory->create(static::getTestedType());

        $formChoices = $form->createView()->vars['choices'] ?? [];
        self::assertCount(981, $formChoices);
        self::assertContainsEquals(new ChoiceView('0763', '0763', '0763 Agricultural Co-operatives'), $formChoices);

        $mccs = array_map(static fn (ChoiceView $choice) => $choice->value, $formChoices);
        self::assertSameSize($mccs, array_unique($mccs), 'MCC should contain unique values');
    }

    public function testItShowsMccsFromDatabaseIfTheyArePresent(): void
    {
        $this->settings->method('getValue')->with(SettingsInterface::MCC_LIST)->willReturn([
            'Real estate or construction' => [
                5039 => 'Construction',
                5074 => 'Plumbing and heating',
            ],
            'Charity or not-for-profit' => [
                8398 => 'All activities',
            ],
        ]);

        $form = $this->factory->create(static::getTestedType());

        $formChoices = $form->createView()->vars['choices'] ?? [];
        self::assertCount(3, $formChoices);
        self::assertContainsEquals(new ChoiceView('5039', '5039', '5039 Construction'), $formChoices);
        self::assertContainsEquals(new ChoiceView('5074', '5074', '5074 Plumbing and heating'), $formChoices);
        self::assertContainsEquals(new ChoiceView('8398', '8398', '8398 All activities'), $formChoices);

        $mccs = array_map(static fn (ChoiceView $choice) => $choice->value, $formChoices);
        self::assertSameSize($mccs, array_unique($mccs), 'MCC should contain unique values');
    }

    public function testItSubmitsInvalidValue(mixed $value = '1763'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = '0763'): void
    {
        parent::testItSubmitsValidValue($value);
    }

    protected static function getTestedType(): string
    {
        return MccType::class;
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new MccType($this->settings)], []),
        ];
    }
}
