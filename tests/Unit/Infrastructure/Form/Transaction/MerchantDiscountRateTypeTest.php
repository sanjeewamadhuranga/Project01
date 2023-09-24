<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Transaction;

use App\Domain\Document\SubscriptionPlan\SubscriptionPlan;
use App\Domain\Document\Transaction\MerchantDiscountRate;
use App\Infrastructure\Form\Transaction\MerchantDiscountRateType;
use App\Infrastructure\Repository\SubscriptionPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class MerchantDiscountRateTypeTest extends TypeTestCase
{
    private SubscriptionPlanRepository&Stub $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createStub(SubscriptionPlanRepository::class);

        parent::setUp();
    }

    public function testItGetsChoicesFromSubscriptionPlanRepository(): void
    {
        $rate1Code = 'rate1Code';
        $rate2Code = 'rate2Code';
        $rate3Code = 'rate3Code';

        $rate1 = new MerchantDiscountRate();
        $rate2 = new MerchantDiscountRate();
        $rate3 = new MerchantDiscountRate();

        $rate1->setCode($rate1Code);
        $rate2->setCode($rate2Code);
        $rate3->setCode($rate3Code);

        $subscriptionPlan = $this->createStub(SubscriptionPlan::class);
        $subscriptionPlan->method('getMerchantDiscountRates')->willReturn(new ArrayCollection([$rate1, $rate2, $rate3]));

        $this->repository->method('findOneBy')->willReturn($subscriptionPlan);

        $form = $this->factory->create(MerchantDiscountRateType::class);

        /** @var CallbackChoiceLoader $callbackChoiceLoader */
        $callbackChoiceLoader = $form->getConfig()->getOptions()['choice_loader'];

        self::assertSame([
            $rate1Code => $rate1Code,
            $rate2Code => $rate2Code,
            $rate3Code => $rate3Code,
        ], $callbackChoiceLoader->loadChoiceList()->getOriginalKeys());
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new MerchantDiscountRateType($this->repository)], []),
        ];
    }
}
