<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Provider\CDEUsage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CDEUsageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('preAuthorize', CheckboxType::class, [
                'required' => false,
                'label' => 'config_provider.label.cdeUsage.preAuthorize',
            ])
            ->add('authorization', CheckboxType::class, [
                'required' => false,
                'label' => 'config_provider.label.cdeUsage.authorization',
            ])
            ->add('capture', CheckboxType::class, [
                'required' => false,
                'label' => 'config_provider.label.cdeUsage.capture',
            ])
            ->add('void', CheckboxType::class, [
                'required' => false,
                'label' => 'config_provider.label.cdeUsage.void',
            ])
            ->add('refund', CheckboxType::class, [
                'required' => false,
                'label' => 'config_provider.label.cdeUsage.refund',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CDEUsage::class);
    }
}
