<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Domain\Document\Compliance\Dispute;
use App\Infrastructure\Form\DataTransformer\IdToTransactionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisputeSelectTransactionType extends AbstractType
{
    public function __construct(private readonly IdToTransactionTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transaction', TextType::class, [
                'label' => 'compliance.disputes.label.transaction',
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
