<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Provider;

use App\Domain\Document\Provider\Provider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RefundsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('refundWindowDays', IntegerType::class, [
                'label' => 'config_provider.label.refundWindowDays',
                'required' => false,
                'attr' => ['min' => 0],
            ])
            ->add('canRefundIfConfirmed', CheckboxType::class, [
                'label' => 'config_provider.label.paymentRefund',
                'required' => false,
            ])
            ->add('canPartialRefundIfConfirmed', CheckboxType::class, [
                'label' => 'config_provider.label.paymentPartialRefund',
                'required' => false,
            ])
            ->add('canIncrementalPartialRefundIfConfirmed', CheckboxType::class, [
                'label' => 'config_provider.label.canIncrementalPartialRefundIfConfirmed',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Provider::class);
        $resolver->setDefault('inherit_data', true);
    }
}
