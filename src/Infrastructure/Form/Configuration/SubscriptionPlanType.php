<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\SubscriptionPlan\SubscriptionPlan;
use App\Infrastructure\Form\Configuration\SubscriptionPlan\AccountLimitsType;
use App\Infrastructure\Form\Configuration\SubscriptionPlan\MerchantDiscountRateType;
use App\Infrastructure\Form\Type\EnabledCurrencyType;
use App\Infrastructure\Form\Type\EnabledFeatureType;
use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'subscription_plan.label.name',
            ])
            ->add('code', TextType::class, [
                'label' => 'subscription_plan.label.code',
            ])
            ->add('description', TextType::class, [
                'label' => 'subscription_plan.label.description',
            ])
            ->add('modification', TextType::class, [
                'label' => 'subscription_plan.label.modification',
            ])
            ->add('dccMarkupRate', PercentType::class, [
                'label' => 'subscription_plan.label.dccMarkupRate',
                'type' => 'integer',
                'rounding_mode' => NumberFormatter::ROUND_HALFUP,
                'html5' => true,
            ])
            ->add('dccMerchantRebateRate', PercentType::class, [
                'label' => 'subscription_plan.label.dccMerchantRebateRate',
                'type' => 'integer',
                'rounding_mode' => NumberFormatter::ROUND_HALFUP,
                'html5' => true,
            ])
            ->add('instantActivation', CheckboxType::class, [
                'label' => 'subscription_plan.label.instantActivationDescription',
                'required' => false,
            ])
            ->add('userPublic', CheckboxType::class, [
                'label' => 'subscription_plan.label.userPublic',
                'required' => false,
            ])
            ->add('supportLevel', ChoiceType::class, [
                'label' => 'subscription_plan.label.supportLevel',
                'required' => false,
                'choice_translation_domain' => false,
                'choices' => [
                    'VIP' => 'VIP',
                    'BASIC' => 'BASIC',
                ],
            ])
            ->add('pricePerMonth', MoneyType::class, [
                'label' => 'subscription_plan.label.pricePerMonth',
                'required' => false,
                'currency' => false,
            ])
            ->add('pricePerYear', MoneyType::class, [
                'label' => 'subscription_plan.label.pricePerYear',
                'required' => false,
                'currency' => false,
            ])
            ->add('priceCurrency', EnabledCurrencyType::class, [
                'label' => 'subscription_plan.label.priceCurrency',
                'required' => false,
            ])
            ->add('accountLimits', AccountLimitsType::class, [
                'label' => 'subscription_plan.label.accountLimits',
            ])
            ->add('merchantDiscountRates', CollectionType::class, [
                'label' => 'subscription_plan.label.merchantDiscountRates',
                'entry_type' => MerchantDiscountRateType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('restrictedFeatures', EnabledFeatureType::class, [
                'label' => 'subscription_plan.label.restrictedFeatures',
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'tom-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SubscriptionPlan::class);
    }
}
