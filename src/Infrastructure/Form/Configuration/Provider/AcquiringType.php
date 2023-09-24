<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Provider;

use App\Domain\Document\Provider\Provider;
use App\Infrastructure\Form\Type\EnabledCurrencyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AcquiringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isSecAcqRequiredForThisPrimary', CheckboxType::class, [
                'label' => 'config_provider.label.isSecAcqRequiredForThisPrimary',
                'required' => false,
            ])
            ->add('secondaryAcquiringAllowedCurrencies', EnabledCurrencyType::class, [
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'tom-select'],
            ])
            ->add('useForSecondaryAcquiring', CheckboxType::class, [
                'label' => 'config_provider.label.useForSecondaryAcquiring',
                'required' => false,
            ])
            ->add('secondaryAcquiringProgrammeId', TextType::class, [
                'label' => 'config_provider.label.secondaryAcquiringProgrammeId',
                'required' => false,
            ])
            ->add('secondaryAcquiringCdeUrl', TextType::class, [
                'label' => 'config_provider.label.secondaryAcquiringCdeUrl',
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
