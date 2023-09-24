<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Domain\Compliance\DisputeReason;
use App\Domain\Document\Compliance\Dispute;
use App\Infrastructure\Form\DataTransformer\IdToTransactionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisputeType extends AbstractType
{
    public function __construct(private readonly IdToTransactionTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transaction', TextType::class, [
                'label' => 'compliance.disputes.label.transaction',
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('reason', EnumType::class, [
                'label' => 'compliance.disputes.label.reason',
                'class' => DisputeReason::class,
                'choice_translation_domain' => false,
            ])
            ->add('disputeFee', MoneyType::class, [
                'required' => false,
                'divisor' => 100,
                'html5' => true,
                'currency' => $builder->getData()->getTransaction()->getCurrency(),
                'label' => 'compliance.disputes.label.disputeFee',
                'attr' => [
                    'step' => 0.01,
                ],
            ])
            ->add('comments', TextareaType::class, [
                'label' => 'compliance.disputes.label.comments',
            ])
            ->get('transaction')
            ->addModelTransformer($this->transformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Dispute::class);
    }
}
