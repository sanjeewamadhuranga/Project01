<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\DiscountCode;
use App\Infrastructure\Form\Type\WeekdayType;
use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('active', CheckboxType::class, [
                'label' => 'discount_code.label.active',
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'discount_code.label.title',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'discount_code.label.description',
            ])
            ->add('code', TextType::class, [
                'label' => 'discount_code.label.code',
            ])
            ->add('discountPercentage', PercentType::class, [
                'label' => 'discount_code.label.discountPercentage',
                'type' => 'integer',
                'rounding_mode' => NumberFormatter::ROUND_HALFUP,
                'html5' => true,
            ])
            ->add('totalDiscountCallback', ChoiceType::class, [
                'label' => 'discount_code.label.totalDiscountCallback',
                'choice_translation_domain' => false,
                'choices' => [
                    'EatOutToHelpOut' => 'EatOutToHelpOut',
                ],
            ])
            ->add('validFrom', DateTimeType::class, [
                'label' => 'discount_code.label.validFrom',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('validTo', DateTimeType::class, [
                'label' => 'discount_code.label.validTo',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('daysValid', WeekdayType::class, [
                'label' => 'discount_code.label.daysValid',
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', DiscountCode::class);
    }
}
