<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Infrastructure\Repository\ProviderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProviderSettingType extends AbstractType
{
    public function __construct(private readonly ProviderRepository $providerRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $providers = $this->providerRepository->findAll();
        $choices = [];
        foreach ($providers as $provider) {
            $choices[$provider->getTitle().' ('.$provider->getValue().')'] = $provider->getValue();
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
