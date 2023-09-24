<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Domain\Document\Compliance\CaseFlow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaseApproveFlowType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('approveComments', TextareaType::class, [
                'required' => false,
                'label' => 'compliance.case.label.additional_comments',
                'attr' => [
                    'placeholder' => 'compliance.case.placeholder.comments',
                    'rows' => '10',
                ],
            ])
            ->add('approved', CheckboxType::class, [
                'mapped' => false,
                'label' => 'compliance.case.label.approved',
                'label_attr' => [
                    'class' => 'compliance-case-checkbox-label',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'compliance.case.label.submit',
                'attr' => [
                    'class' => 'btn btn-primary me-1 mb-1 float-end',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CaseFlow::class);
    }
}
