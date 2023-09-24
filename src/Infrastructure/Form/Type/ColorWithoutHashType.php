<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Infrastructure\Form\DataTransformer\StringToColorTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColorWithoutHashType extends AbstractType
{
    public function __construct(private readonly StringToColorTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'html5' => true,
        ]);
    }

    public function getParent(): string
    {
        return ColorType::class;
    }
}
