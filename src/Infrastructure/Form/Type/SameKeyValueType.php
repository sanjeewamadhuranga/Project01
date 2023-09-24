<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Creates a collection form which allows users to specify key and value of associative array.
 *
 * @implements DataTransformerInterface<mixed, list<array{default: bool, value: string|null}>>
 */
class SameKeyValueType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer($this)
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event): void {
                $input = $event->getData();

                if (null === $input) {
                    return;
                }

                $output = [];

                foreach ($input as $key => $value) {
                    if ('default' === $key) {
                        continue;
                    }

                    $output[] = [
                        'default' => ($input['default'] ?? null) === $key,
                        'value' => $value,
                    ];
                }

                $event->setData($output);
            }, 1)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => DefaultableEntryType::class,
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true,
        ]);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function transform($value): mixed
    {
        if (null === $value) {
            return [];
        }

        return array_values($value);
    }

    /**
     * @return array<string, string|null>
     */
    public function reverseTransform($value): array
    {
        $modelValue = ['default' => null];

        foreach ($value ?? [] as $data) {
            if (['default', 'value'] !== array_keys($data)) { // @phpstan-ignore-line
                throw new TransformationFailedException();
            }

            if (true === $data['default']) {
                $modelValue['default'] = $data['value'];
            }

            $modelValue[(string) $data['value']] = (string) $data['value'];
        }

        return $modelValue;
    }
}
