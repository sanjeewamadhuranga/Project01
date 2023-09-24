<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\SubscriptionPlan;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberLimitType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'html5' => true,
            'attr' => ['min' => 0],
        ]);
    }

    public function getParent(): string
    {
        return NumberType::class;
    }
}
