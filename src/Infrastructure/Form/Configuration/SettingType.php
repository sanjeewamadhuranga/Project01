<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Application\Settings\Type;
use App\Domain\Document\Setting;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\BooleanStringType;
use App\Infrastructure\Form\Type\KeyValueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UnexpectedValueException;

class SettingType extends AbstractType
{
    public function __construct(private readonly SystemSettings $settings)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $missingKeys = array_diff(array_values(SystemSettings::getConstants()), array_keys($this->settings->getAll()));
        sort($missingKeys);

        $builder->add('name', ChoiceType::class, [
            'choices' => array_combine($missingKeys, $missingKeys),
            'choice_translation_domain' => false,
            'label' => 'config_settings.label.name',
        ]);

        $valueLabel = 'config_settings.label.value';

        // Hide setting value if it's marked as masked
        $getter = static fn (?Setting $setting) => $setting?->getMaskedValue(true);

        match ($options['type']) {
            Type::PLAIN => $builder->add('value', TextType::class, ['label' => $valueLabel, 'getter' => $getter]),
            Type::OBJECT => $builder->add('value', KeyValueType::class, ['label' => false, 'getter' => fn (?Setting $setting) => (array) $getter($setting)]),
            Type::COLLECTION => $this->addCollectionValueField($builder, $valueLabel, fn (?Setting $setting) => (array) $getter($setting)),
            Type::BOOL => $builder->add('value', BooleanStringType::class, ['label' => $valueLabel]),
            default => throw new UnexpectedValueException('Invalid type provided'),
        };

        if (Type::BOOL !== $options['type']) {
            $builder->add('maskValue', CheckboxType::class, [
                'required' => false,
                'label' => 'config_settings.label.maskValue',
            ]);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['type'] = $options['type'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Setting::class);
        $resolver->define('type')->allowedTypes(Type::class)->default(Type::PLAIN);
    }

    private function addCollectionValueField(FormBuilderInterface $builder, string $label, callable $getter): void
    {
        $builder->add('value', CollectionType::class, [
            'entry_type' => TextType::class,
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => [
                'label' => $label,
            ],
            'label' => false,
            'getter' => $getter,
        ])
            ->get('value')
            // It is necessary to convert the data to a "list" before saving so it's not persisted as object
            ->addModelTransformer(new CallbackTransformer(
                fn ($value) => array_values((array) $value),
                fn ($value) => array_values((array) $value)
            ))
        ;
    }
}
