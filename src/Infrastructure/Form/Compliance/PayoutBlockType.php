<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Domain\Compliance\PayoutBlockReason;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Infrastructure\Form\Type\CompanyAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayoutBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('company', CompanyAutocompleteType::class, [
                'label' => 'compliance.case.label.company',
            ])
            ->add('reason', EnumType::class, [
                'placeholder' => 'compliance.case.placeholder.reason',
                'label' => 'compliance.case.label.reason',
                'class' => PayoutBlockReason::class,
                'choice_translation_domain' => false,
            ])
            ->add('comments', TextareaType::class, [
                'required' => false,
                'label' => 'compliance.case.label.comments',
                'attr' => [
                    'placeholder' => 'compliance.case.placeholder.comments',
                    'rows' => '5',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', PayoutBlock::class);
    }
}
