<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Domain\Document\Compliance\RiskProfile;
use App\Infrastructure\Form\Type\EnabledCurrencyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RiskProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true,
                'label' => 'compliance.risk_profiles.label.code',
                'attr' => [
                    'placeholder' => 'compliance.risk_profiles.placeholder.code',
                ],
            ])
            ->add('currency', EnabledCurrencyType::class, [
                'required' => true,
                'label' => 'currency_fx.label.currency',
                'placeholder' => 'compliance.risk_profiles.placeholder.currency',
            ])
            ->add('singleTransactionAmount', MoneyType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.singleTransactionAmount',
                'currency' => null,
                'divisor' => 100,
                'html5' => true,
                'attr' => [
                    'placeholder' => 'compliance.risk_profiles.placeholder.amount',
                    'step' => 0.01,
                ],
            ])
            ->add('numberOfConfirmedTransactions', NumberType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.numberOfConfirmedTransactions',
                'attr' => [
                    'placeholder' => 'compliance.risk_profiles.placeholder.numberOfConfirmedTransactions',
                ],
            ])
            ->add('numberOfConfirmedTransactionsTimeFrame', ChoiceType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.numberOfConfirmedTransactionsTimeFrame',
                'placeholder' => 'compliance.risk_profiles.placeholder.numberOfConfirmedTransactionsTimeFrame',
                'choices' => array_combine(range(1, 31), range(1, 31)),
            ])
            ->add('dailyAmount', MoneyType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.dailyAmount',
                'currency' => null,
                'divisor' => 100,
                'html5' => true,
                'attr' => [
                    'placeholder' => 'compliance.risk_profiles.placeholder.amount',
                    'step' => 0.01,
                ],
            ])
            ->add('weeklyAmount', MoneyType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.weeklyAmount',
                'currency' => null,
                'divisor' => 100,
                'html5' => true,
                'attr' => [
                    'placeholder' => 'compliance.risk_profiles.placeholder.amount',
                    'step' => 0.01,
                ],
            ])
            ->add('monthlyAmount', MoneyType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.monthlyAmount',
                'currency' => null,
                'divisor' => 100,
                'html5' => true,
                'attr' => [
                    'placeholder' => 'compliance.risk_profiles.placeholder.amount',
                    'step' => 0.01,
                ],
            ])
            ->add('duplicateBuyerId', CheckboxType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.duplicateBuyerId',
            ])
            ->add('duplicateBuyerIdTimeFrame', ChoiceType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.duplicateBuyerIdTimeFrame',
                'placeholder' => 'compliance.risk_profiles.placeholder.duplicateBuyerIdTimeFrame',
                'choices' => array_combine(range(1, 60), range(1, 60)),
            ])
            ->add('duplicateAmount', CheckboxType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.duplicateAmount',
            ])
            ->add('duplicateAmountTimeFrame', ChoiceType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.duplicateAmountTimeFrame',
                'placeholder' => 'compliance.risk_profiles.placeholder.duplicateBuyerIdTimeFrame',
                'choices' => array_combine(range(1, 60), range(1, 60)),
            ])
            ->add('allowedTimeIntervalStart', TimeType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.allowedTimeIntervalStart',
                'input' => 'string',
                'input_format' => 'H:i',
            ])
            ->add('allowedTimeIntervalEnd', TimeType::class, [
                'required' => false,
                'label' => 'compliance.risk_profiles.label.allowedTimeIntervalEnd',
                'input' => 'string',
                'input_format' => 'H:i',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', RiskProfile::class);
    }
}
