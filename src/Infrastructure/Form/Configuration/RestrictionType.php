<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Rule\Restriction;
use App\Domain\Restriction\ComparisonType;
use App\Domain\Restriction\FieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestrictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('field', EnumType::class, [
                'class' => FieldType::class,
                'choice_label' => 'readable',
                'label' => 'config_rules.restriction.field',
                'attr' => [
                    'class' => 'form-select-sm',
                ],
            ])
            ->add('comparison', EnumType::class, [
                'choice_label' => 'readable',
                'class' => ComparisonType::class,
                'label' => 'config_rules.restriction.comparison',
                'attr' => [
                    'class' => 'form-select-sm',
                ],
            ])
            ->add('value', TextareaType::class, [
                'label' => 'config_rules.restriction.value',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Restriction::class);
    }
}
