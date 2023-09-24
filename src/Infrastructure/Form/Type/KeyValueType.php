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
 * It performs necessary transformations so that ["EUR" => "100"] is displayed as [["key" => "EUR", "value" => "100"]].
 *
 * @implements DataTransformerInterface<mixed, list<array{key: string|null, value: string|null}>>
 */
class KeyValueType extends AbstractType implements DataTransformerInterface
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
                    $output[] = [
                        'key' => $key,
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
            'entry_type' => KeyValueEntryType::class,
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
        return $value;
    }

    /**
     * @return array<string, string>
     */
    public function reverseTransform($value): array
    {
        $modelValue = [];

        foreach ($value ?? [] as $data) {
            if (['key', 'value'] !== array_keys($data)) { // @phpstan-ignore-line
                throw new TransformationFailedException();
            }

            $modelValue[(string) $data['key']] = (string) $data['value'];
        }

        return $modelValue;
    }
}
