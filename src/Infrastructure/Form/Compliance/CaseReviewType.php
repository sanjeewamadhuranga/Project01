<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Security\Administrator;
use DateTime;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaseReviewType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('caseFlow', CaseReviewFlowType::class)
            ->add('assignTo', ChoiceType::class, [
                'mapped' => false,
                'label' => false,
                'choices' => [
                    'compliance.case.label.submit_unassigned' => 'unassigned',
                    'compliance.case.label.assign_to_user' => 'assign_to_user',
                ],
                'choice_attr' => [
                    'compliance.case.label.submit_unassigned' => [
                        'help' => 'compliance.case.text.unassigned',
                        'checked' => 'checked',
                    ],
                    'compliance.case.label.assign_to_user' => [
                        'help' => 'compliance.case.text.assign_to_user',
                    ],
                ],
                'expanded' => true,
                'attr' => [
                    'onclick' => 'approverToggle()',
                ],
            ])
            ->add('approver', DocumentType::class, [
                'class' => Administrator::class,
                'required' => false,
                'label' => 'compliance.case.label.assigned_approver',
                'attr' => [
                    'placeholder' => 'compliance.case.placeholder.select_user',
                ],
            ])
            ->add('muteNewFlagsCheck', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'compliance.case.label.mute_new_flags_check',
                'attr' => [
                    'onclick' => 'muteNewFlagsToggle()',
                ],
                'help' => 'compliance.case.text.mute_new_flags',
            ])
            ->add('ignoreDate', DateType::class, [
                'required' => false,
                'label' => 'compliance.case.label.mute_new_flags',
                'data' => new DateTime(),
                'widget' => 'single_text',
                'label_attr' => [
                    'class' => 'compliance-case-datetime-label',
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
        $resolver->setDefault('data_class', PayoutBlock::class);
    }
}
