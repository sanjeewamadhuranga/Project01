<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Provider;

use App\Domain\Document\Provider\Provider;
use App\Infrastructure\Form\Type\EnabledCurrencyType;
use App\Infrastructure\Form\Type\MdrCodeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettlementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currencies', EnabledCurrencyType::class, [
                'label' => 'config_provider.label.currencies',
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'tom-select'],
            ])
            ->add('payCurrencies', EnabledCurrencyType::class, [
                'label' => 'config_provider.label.payCurrencies',
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'tom-select'],
            ])
            ->add('fixedMdrCode', MdrCodeType::class, [
                'label' => 'config_provider.label.fixedMdrCode',
            ])
            ->add('autocredit', CheckboxType::class, [
                'label' => 'config_provider.label.autoCredit',
                'required' => false,
            ])
            ->add('externalImport', CheckboxType::class, [
                'label' => 'config_provider.label.externalImport',
                'required' => false,
            ])
            ->add('excludeFromRemittance', CheckboxType::class, [
                'label' => 'config_provider.label.excludeFromRemittance',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Provider::class);
        $resolver->setDefault('inherit_data', true);
    }
}
