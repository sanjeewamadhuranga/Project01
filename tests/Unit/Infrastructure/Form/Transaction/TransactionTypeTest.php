<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Transaction;

use App\Domain\Document\Company\Company;
use App\Domain\Document\SubscriptionPlan\SubscriptionPlan;
use App\Domain\Document\Transaction\MerchantDiscountRate;
use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\Form\Transaction\MerchantDiscountRateType;
use App\Infrastructure\Form\Transaction\TransactionType;
use App\Infrastructure\Repository\SubscriptionPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class TransactionTypeTest extends TypeTestCase
{
    private SubscriptionPlanRepository&Stub $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createStub(SubscriptionPlanRepository::class);

        parent::setUp();
    }

    public function testItGetsMerchantDiscountRateCodeFromMerchantSubscriptionPlaneAndCurrencyFromTransaction(): void
    {
        $currency = 'USD';
        $code = uniqid();

        $transaction = $this->getTransaction($currency, $code);
        $form = $this->factory->create(TransactionType::class, $transaction);

        self::assertSame($code, $form->get('merchantDiscountRateCode')->getConfig()->getOptions()['code']);
        self::assertSame($currency, $form->get('availableBalance')->getConfig()->getOptions()['currency']);
    }

    public function testItGetsProvidersFromMerchantEnabledProviders(): void
    {
        $provider1 = uniqid();
        $provider2 = uniqid();
        $provider3 = uniqid();
        $enabledProviders = [$provider1 => $provider1, $provider2 => $provider2, $provider3 => $provider3];

        $transaction = $this->getTransaction('GBP', 'code', $enabledProviders);
        $form = $this->factory->create(TransactionType::class, $transaction);

        self::assertSame($enabledProviders, $form->get('provider')->getConfig()->getOptions()['choices']);
    }

    /**
     * @param array<string, string> $enabledProviders
     */
    private function getTransaction(string $currency, string $code, array $enabledProviders = []): Transaction
    {
        $merchant = new Company();
        $merchant->setSubscriptionPlan($code);
        $merchant->setEnabledProviders($enabledProviders);

        $merchantDiscountRate = new MerchantDiscountRate();
        $merchantDiscountRate->setCode($code);

        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->setMerchantDiscountRates(new ArrayCollection([$merchantDiscountRate]));

        $this->repository->method('findOneBy')->willReturn($subscriptionPlan);

        $transaction = new Transaction();
        $transaction->setCurrency($currency);
        $transaction->setMerchant($merchant);

        return $transaction;
    }

    /**
     * @return array<int, PreloadedExtension|ValidatorExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new MerchantDiscountRateType($this->repository)], []),
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
