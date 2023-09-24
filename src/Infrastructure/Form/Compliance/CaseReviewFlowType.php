<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Domain\Document\Compliance\CaseFlow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class CaseReviewFlowType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('reviewComments', TextareaType::class, [
                'required' => false,
                'label' => 'compliance.case.label.task_comments',
                'attr' => [
                    'placeholder' => 'compliance.case.placeholder.comments',
                    'rows' => '10',
                ],
            ])
            ->add('finaliseReview', CheckboxType::class, [
                'mapped' => false,
                'label' => 'compliance.case.label.submit_for_approval_check',
                'label_attr' => [
                    'class' => 'compliance-case-checkbox-label',
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'compliance.case.text.finalise_review_check',
                    ]),
                ],
            ])
            ->add('suggestedAction', TextType::class, [
                'required' => false,
                'label' => 'compliance.case.label.suggested_action',
                'attr' => [
                    'placeholder' => 'compliance.case.placeholder.select',
                ],
            ])
            ->add('merchantFundsAction', TextType::class, [
                'required' => false,
                'label' => 'compliance.case.label.merchant_funds_action',
                'attr' => [
                    'placeholder' => 'compliance.case.placeholder.select',
                ],
            ])
            ->add('submitForApproval', ButtonType::class, [
                'label' => 'compliance.case.label.submit_for_approval',
                'attr' => [
                    'class' => 'btn btn-primary me-1 mb-1 float-end',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#submitReviewModal',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CaseFlow::class);
    }
}
