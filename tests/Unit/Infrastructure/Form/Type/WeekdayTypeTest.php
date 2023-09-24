<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Infrastructure\Form\Type\WeekdayType;
use Carbon\Carbon;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

class WeekdayTypeTest extends ChoiceTypeTest
{
    public function testAvailableChoices(array $choices = []): void
    {
        $choiceViews = [];
        foreach (Carbon::getDays() as $index => $day) {
            $choiceViews[] = new ChoiceView($index, (string) $index, $day);
        }

        parent::testAvailableChoices($choiceViews);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'GHI'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = 0): void
    {
        parent::testItSubmitsValidValue($value);
    }

    protected static function getTestedType(): string
    {
        return WeekdayType::class;
    }
}
