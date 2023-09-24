<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\SubscriptionPlan;

use App\Domain\Document\Transaction\MerchantDiscountRate;
use App\Infrastructure\Form\Type\MdrCodeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MerchantDiscountRateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', MdrCodeType::class, [
                'label' => 'subscription_plan.label.discountRate.mdr',
            ])
            ->add('fixedFee', MoneyType::class, [
                'label' => 'subscription_plan.label.discountRate.fixedFee',
                'currency' => false,
                'divisor' => 100,
                'attr' => [
                    'step' => 0.01,
                ],
            ])
            ->add('rateFee', MoneyType::class, [
                'label' => 'subscription_plan.label.discountRate.rateFee',
                'divisor' => 100,
                'currency' => false,
                'attr' => [
                    'step' => 0.01,
                ],
            ])
            ->add('provider', TextType::class, [
                'label' => 'subscription_plan.label.discountRate.provider',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', MerchantDiscountRate::class);
    }
}
