<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Provider;

use App\Domain\Document\Provider\Provider;
use App\Domain\Provider\EcommercePreferredMode;
use App\Infrastructure\Form\Type\ColorWithoutHashType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresentationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'config_provider.label.description',
            ])
            ->add('customerDescription', TextareaType::class, [
                'label' => 'config_provider.label.customerDescription',
            ])
            ->add('ecommercePreferredMode', EnumType::class, [
                'class' => EcommercePreferredMode::class,
                'choice_label' => 'readable',
                'placeholder' => 'merchant.select_one',
                'label' => 'config_provider.label.ecommercePreferredMode',
                'required' => false,
            ])
            ->add('brandColor', ColorWithoutHashType::class, [
                'label' => 'config_provider.label.brandColor',
                'required' => false,
            ])
            ->add('sortPosition', NumberType::class, [
                'label' => 'config_provider.label.sortPosition',
                'required' => false,
            ])
            ->add('hiddenOnMobileResponsiveView', CheckboxType::class, [
                'label' => 'config_provider.label.hiddenOnMobileResponsiveView',
                'required' => false,
            ])
            ->add('excludeFromOptionListMobilePos', CheckboxType::class, [
                'label' => 'config_provider.label.excludeFromOptionListMobilePos',
                'required' => false,
            ])
            ->add('excludeFromOptionListPaymentPortal', CheckboxType::class, [
                'label' => 'config_provider.label.excludeFromOptionListPaymentPortal',
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
