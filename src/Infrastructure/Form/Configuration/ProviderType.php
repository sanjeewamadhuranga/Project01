<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Provider\Provider;
use App\Infrastructure\Form\Configuration\Provider\AcquiringType;
use App\Infrastructure\Form\Configuration\Provider\AdvancedConfigType;
use App\Infrastructure\Form\Configuration\Provider\ConfigurationType;
use App\Infrastructure\Form\Configuration\Provider\FXType;
use App\Infrastructure\Form\Configuration\Provider\GeneralType;
use App\Infrastructure\Form\Configuration\Provider\LogoType;
use App\Infrastructure\Form\Configuration\Provider\PresentationType;
use App\Infrastructure\Form\Configuration\Provider\RefundsType;
use App\Infrastructure\Form\Configuration\Provider\SettlementType;
use App\Infrastructure\Form\Type\CurrencyLimitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProviderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('general', GeneralType::class, ['label' => false])
            ->add('logos', LogoType::class, ['label' => false])
            ->add('presentation', PresentationType::class, ['label' => false])
            ->add('settlement', SettlementType::class, ['label' => false])
            ->add('configuration', ConfigurationType::class, ['label' => false])
            ->add('refunds', RefundsType::class, ['label' => false])
            ->add('acquiring', AcquiringType::class, ['label' => false])
            ->add('fxConfig', FXType::class, ['label' => false])
            ->add('advanceConfig', AdvancedConfigType::class, ['label' => false])
            ->add('currencyLimits', CollectionType::class, [
                'entry_type' => CurrencyLimitType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'config_provider.legend.currencyLimit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Provider::class);
    }
}
