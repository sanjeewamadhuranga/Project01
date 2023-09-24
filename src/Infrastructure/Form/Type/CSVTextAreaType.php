<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Creates a common form type for CSV TextArea input fields.
 * This allows users to specify CSV in the form field and stores the data as a string array after data transformation and necessary validations.
 * It performs transformations so that "test1, test2, test3" is stored as ["test1", "test2", "test3"].
 *
 * @implements DataTransformerInterface<string[], string>
 */
class CSVTextAreaType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['rows' => '5'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function transform(mixed $value): ?string
    {
        return null !== $value && count($value) > 0 ? implode(',', $value) : null;
    }

    /**
     * @return string[]|null
     */
    public function reverseTransform(mixed $value): ?array
    {
        if (null === $value) {
            return null;
        }

        return array_filter(array_map(static fn (string $input) => trim((string) preg_replace('/[\r\n\t]/', '', $input)), explode(',', $value)));
    }
}
