<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Rule\Rule;
use App\Domain\Rule\DecisionType;
use App\Domain\Rule\EventType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'config_rules.label.name',
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'active',
                'required' => false,
            ])
            ->add('event', EnumType::class, [
                'class' => EventType::class,
                'choice_label' => 'readable',
                'placeholder' => 'merchant.select_one',
                'label' => 'config_rules.label.event',
            ])
            ->add('decision', EnumType::class, [
                'class' => DecisionType::class,
                'choice_label' => 'readable',
                'placeholder' => 'merchant.select_one',
                'label' => 'config_rules.label.decision',
            ])
            ->add('restrictions', CollectionType::class, [
                'entry_type' => RestrictionType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('decisionData', TextType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Rule::class);
    }
}
