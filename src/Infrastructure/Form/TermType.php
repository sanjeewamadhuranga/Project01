<?php

declare(strict_types=1);

namespace App\Infrastructure\Form;

use App\Domain\Document\Term\AbstractTerms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TermType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('termsAndConditions', TextareaType::class, [
                'label' => 'merchant.terms.label.tnc',
                'attr' => ['rows' => 10],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', AbstractTerms::class);
    }
}
