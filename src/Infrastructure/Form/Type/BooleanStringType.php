<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Infrastructure\Form\DataTransformer\StringToBooleanDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Create a toggle switch form which allow user to submit true/false in string type.
 */
class BooleanStringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new StringToBooleanDataTransformer())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'label_attr' => ['class' => 'checkbox-switch'],
        ]);
    }

    public function getParent(): string
    {
        return CheckboxType::class;
    }
}
