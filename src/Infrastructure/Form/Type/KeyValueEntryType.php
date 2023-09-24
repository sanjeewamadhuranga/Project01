<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KeyValueEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('key', $options['key_type'], $options['key_options'])
            ->add('value', $options['value_type'], $options['value_options'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'key_type' => TextType::class,
            'key_options' => [],
            'value_type' => TextType::class,
            'value_options' => [],
        ]);

        $resolver->setAllowedTypes('key_options', 'array');
        $resolver->setAllowedTypes('value_options', 'array');
    }
}
