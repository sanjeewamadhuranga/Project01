<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Domain\Settings\SystemSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MdrCodeType extends AbstractType
{
    public function __construct(private readonly SystemSettings $systemSettings)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $codes = $this->systemSettings->getMdrCodes();
        $resolver->setDefaults([
            'choices' => array_combine($codes, $codes),
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
