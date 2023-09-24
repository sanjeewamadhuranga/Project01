<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\MdrBilling;
use App\Infrastructure\Form\Type\EnabledCurrencyType;
use App\Infrastructure\Form\Type\MdrCodeType;
use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MdrBillingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (in_array('create', $options['validation_groups'] ?? [], true)) {
            $builder->add('mdr', MdrCodeType::class, [
                'label' => 'mdr_billing.label.mdr',
            ]);
        }

        $builder
            ->add('remittance', CheckboxType::class, [
                'label' => 'mdr_billing.label.remittance',
                'required' => false,
            ])
            ->add('fixedFeeCurrency', EnabledCurrencyType::class, [
                'label' => 'mdr_billing.label.fixedFeeCurrency',
                'placeholder' => 'mdr_billing.transactionCurrency',
            ])
            ->add('processingFixed', MoneyType::class, [
                'label' => 'mdr_billing.label.processingFixed',
                'currency' => null,
                'divisor' => 100,
                'html5' => true,
                'attr' => [
                    'step' => 0.01,
                ],
            ])
            ->add('processingPercentage', PercentType::class, [
                'label' => 'mdr_billing.label.processingPercentage',
                'type' => 'integer',
                'rounding_mode' => NumberFormatter::ROUND_HALFUP,
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'step' => 0.01,
                ],
            ])
            ->add('platformFixed', MoneyType::class, [
                'label' => 'mdr_billing.label.platformFixed',
                'currency' => null,
                'divisor' => 100,
                'html5' => true,
                'attr' => [
                    'step' => 0.01,
                ],
            ])
            ->add('platformPercentage', PercentType::class, [
                'label' => 'mdr_billing.label.platformPercentage',
                'type' => 'integer',
                'rounding_mode' => NumberFormatter::ROUND_HALFUP,
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'step' => 0.01,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', MdrBilling::class);
    }
}
