<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use BackedEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Similar to {@see EnumType} but uses enum values as labels, values and actual data.
 */
class EnumValueType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['class'])
            ->setAllowedTypes('class', 'string')
            ->setAllowedValues('class', enum_exists(...))
            ->setDefault('choices', static function (Options $options) {
                $choices = array_map(static fn (BackedEnum $case) => $case->value, $options['class']::cases());

                return array_combine($choices, $choices);
            })
            ->setDefault('choice_translation_domain', false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
