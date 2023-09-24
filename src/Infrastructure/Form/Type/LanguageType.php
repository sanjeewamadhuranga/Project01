<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Domain\Settings\SystemSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageType extends AbstractType
{
    public function __construct(private readonly SystemSettings $systemSettings)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => fn (Options $options): ChoiceLoaderInterface => new CallbackChoiceLoader(function () use ($options): array {
                $choices = array_flip($this->systemSettings->getEnabledLanguages());

                if (true === $options['lowercase']) {
                    return array_map(static fn ($code) => mb_strtolower((string) $code), $choices);
                }

                return $choices;
            }),
            'choices' => array_flip($this->systemSettings->getEnabledLanguages()),
            'empty_data' => $this->systemSettings->getDefaultLanguage(),
            'choice_translation_domain' => false,
            'lowercase' => false,
        ])
            ->setAllowedTypes('lowercase', 'boolean')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
