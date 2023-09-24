<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Domain\Dictionary\Mcc;
use App\Domain\Settings\SystemSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MccType extends AbstractType
{
    public function __construct(private readonly SystemSettings $settings)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
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

    /**
     * @return array<string, string>
     */
    private function getChoices(): array
    {
        $settingMccs = (array) $this->settings->getValue(SystemSettings::MCC_LIST);

        $choices = [];

        if (count($settingMccs) > 0) {
            foreach ($settingMccs as $category) {
                foreach ((array) $category as $code => $name) {
                    $choices[$this->mccLabel((string) $code, $name)] = (string) $code;
                }
            }

            return $choices;
        }

        foreach (Mcc::getList() as $code) {
            $choices[$this->mccLabel($code['mcc'], $code['edited_description'])] = $code['mcc'];
        }

        return $choices;
    }

    private function mccLabel(string $code, string $name): string
    {
        return sprintf('%s %s', $code, substr($name, 0, 110));
    }
}
