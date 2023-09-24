<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Domain\Settings\Features;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnabledFeatureType extends AbstractType
{
    public function __construct(private readonly Features $features)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $features = $this->features->getEnabledFeatures();
        $resolver->setDefaults([
            'choices' => array_combine($features, $features),
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
