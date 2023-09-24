<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Domain\Document\SubscriptionPlan\SubscriptionPlan;
use App\Infrastructure\Form\Type\SubscriptionPlanType;
use App\Infrastructure\Repository\SubscriptionPlanRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;

class SubscriptionPlanTypeTest extends ChoiceTypeTest
{
    private const PLAN_BASIC = 'BASIC';
    private const PLAN_PREMIUM = 'PREMIUM';

    private SubscriptionPlanRepository&MockObject $subscriptionPlanRepository;

    protected function setUp(): void
    {
        $this->subscriptionPlanRepository = $this->createMock(SubscriptionPlanRepository::class);
        $this->subscriptionPlanRepository->method('findAll')->willReturn($this->getSamplePlans());

        parent::setUp();
    }

    public function testAvailableChoices(array $choices = []): void
    {
        parent::testAvailableChoices([
            new ChoiceView(self::PLAN_BASIC, self::PLAN_BASIC, self::PLAN_BASIC),
            new ChoiceView(self::PLAN_PREMIUM, self::PLAN_PREMIUM, self::PLAN_PREMIUM),
        ]);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'TEST'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = self::PLAN_BASIC): void
    {
        parent::testItSubmitsValidValue($value);
    }

    /**
     * @return SubscriptionPlan[]
     */
    protected function getSamplePlans(): array
    {
        $basic = new SubscriptionPlan();
        $basic->setName(self::PLAN_BASIC);

        $premium = new SubscriptionPlan();
        $premium->setName(self::PLAN_PREMIUM);

        return [$basic, $premium];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new SubscriptionPlanType($this->subscriptionPlanRepository)], []),
        ];
    }

    protected static function getTestedType(): string
    {
        return SubscriptionPlanType::class;
    }
}
