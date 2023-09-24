<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Domain\Document\Provider\CurrencyLimit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrencyLimitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency', EnabledCurrencyType::class, [
                'label' => 'currency_limit.currency',
            ])
            ->add('min', MoneyType::class, [
                'currency' => null,
                'divisor' => 100,
                'html5' => true,
                'label' => 'currency_limit.minimum',
                'attr' => [
                    'min' => 0.01,
                    'step' => 0.01,
                ],
            ])
            ->add('max', MoneyType::class, [
                'currency' => null,
                'divisor' => 100,
                'html5' => true,
                'label' => 'currency_limit.maximum',
                'attr' => [
                    'min' => 0.01,
                    'step' => 0.01,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CurrencyLimit::class,
        ]);
    }
}
