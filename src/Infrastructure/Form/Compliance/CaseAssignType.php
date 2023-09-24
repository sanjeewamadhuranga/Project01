<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Application\Compliance\AssignType;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Security\Administrator;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class CaseAssignType extends AbstractType
{
    public function __construct(public readonly Security $security, public readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('assignTo', ChoiceType::class, [
                'mapped' => false,
                'label' => false,
                'choices' => [
                    'compliance.case.label.assign_to_me' => 'self',
                    'compliance.case.label.assign_to_user' => 'user',
                ],
                'choice_attr' => [
                    'compliance.case.label.assign_to_me' => [
                        'help' => 'compliance.case.text.assign_to_me',
                        'checked' => 'checked',
                    ],
                    'compliance.case.label.assign_to_user' => [
                        'help' => 'compliance.case.text.assign_to_user',
                    ],
                ],
                'expanded' => true,
                'attr' => [
                    'onclick' => 'assignerToggle()',
                ],
            ])
            ->add('handler', DocumentType::class, [
                'class' => Administrator::class,
                'required' => false,
                'label' => 'compliance.case.label.assigned_handler',
                'attr' => [
                    'placeholder' => 'compliance.case.placeholder.select_user',
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
            ->add('assignButton', ButtonType::class, [
                'attr' => [
                    'class' => 'btn btn-primary me-1 mb-1 float-end',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#assignModal',
                ],
            ])
            ->add('assignSubmit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary me-1 mb-1 float-end',
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event): void {
                $form = $event->getForm();
                if (null !== $event->getData()->getHandler()) {
                    $form->remove('handler');
                }
                if (null !== $event->getData()->getApprover()) {
                    $form->remove('approver');
                }
            })
            ->addEventListener(
                FormEvents::SUBMIT,
                function (FormEvent $event): void {
                    $form = $event->getForm();
                    $assignTo = $form['assignTo']?->getData();
                    $currentUser = $this->security->getUser();
                    $formError = new FormError($this->translator->trans('compliance.case.message.assign_to_same_user_error'));

                    if (AssignType::SELF === $assignTo
                    && $event->getData()->getHandler() === $currentUser
                    ) {
                        $form->get('approver')->addError($formError);
                    }

                    if (AssignType::SELF === $assignTo
                    && $event->getData()->getApprover() === $currentUser
                    ) {
                        $form->get('handler')->addError($formError);
                    }
                }
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', PayoutBlock::class);
    }
}
