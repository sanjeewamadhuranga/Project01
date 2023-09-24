<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultableEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('default', $options['default_type'], $options['default_options'])
            ->add('value', $options['value_type'], $options['value_options'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'default_type' => CheckboxType::class,
            'default_options' => [
                'required' => false,
            ],
            'value_type' => TextType::class,
            'value_options' => [],
        ]);

        $resolver->setAllowedTypes('default_options', 'array');
        $resolver->setAllowedTypes('value_options', 'array');
    }
}
