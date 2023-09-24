<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Provider;

use App\Domain\Document\Provider\Provider;
use App\Infrastructure\Form\Configuration\CDEUsageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('terminalConstraint', TextType::class, [
                'label' => 'config_provider.label.terminalConstraint',
                'required' => false,
            ])
            ->add('paymentExpiryTime', NumberType::class, [
                'label' => 'config_provider.label.paymentExpiryTime',
                'required' => false,
            ])
            ->add('cdeUsage', CDEUsageType::class, [
                'label' => 'config_provider.label.cdeUsage.header',
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
