<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\SubscriptionPlan;

use App\Domain\Document\SubscriptionPlan\AccountLimits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountLimitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberOfShops', NumberLimitType::class, [
                'label' => 'subscription_plan.label.limits.numberOfShops',
                'required' => false,
            ])
            ->add('numberOfProducts', NumberLimitType::class, [
                'label' => 'subscription_plan.label.limits.numberOfProducts',
                'required' => false,
            ])
            ->add('numberOfIntegrations', NumberLimitType::class, [
                'label' => 'subscription_plan.label.limits.numberOfIntegrations',
                'required' => false,
            ])
            ->add('numberOfUsers', NumberLimitType::class, [
                'label' => 'subscription_plan.label.limits.numberOfUsers',
                'required' => false,
            ])
            ->add('numberOfApps', NumberLimitType::class, [
                'label' => 'subscription_plan.label.limits.numberOfApps',
                'required' => false,
            ])
            ->add('numberOfTerminals', NumberLimitType::class, [
                'label' => 'subscription_plan.label.limits.numberOfTerminals',
                'required' => false,
            ])
            ->add('numberOfConnectedBankAccounts', NumberLimitType::class, [
                'label' => 'subscription_plan.label.limits.numberOfConnectedBankAccounts',
                'required' => false,
            ])
            ->add('payoutOffset', NumberLimitType::class, [
                'label' => 'subscription_plan.label.limits.payoutOffset',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', AccountLimits::class);
    }
}
