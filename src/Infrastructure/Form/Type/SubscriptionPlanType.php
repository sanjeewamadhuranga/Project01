<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Infrastructure\Repository\SubscriptionPlanRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionPlanType extends AbstractType
{
    public function __construct(private readonly SubscriptionPlanRepository $subscriptionPlanRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = [];
        foreach ($this->subscriptionPlanRepository->findAll() as $subscription) {
            $name = $subscription->getName();
            $choices[$name] = $name;
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'choice_translation_domain' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
