<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Settings;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\KeyValueType;
use App\Infrastructure\Form\Type\SameKeyValueType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;

class LocalizationType extends BaseSystemSettingType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(SystemSettings::MANAGER_PORTAL_TIMEZONE, TimezoneType::class, [
                'attr' => ['class' => 'tom-select'],
            ])
            ->add(SystemSettings::ENABLED_TIMEZONES, SameKeyValueType::class, [
                'entry_options' => [
                    'translation_domain' => 'messages',
                    'label_format' => null,
                    'value_type' => TimezoneType::class,
                ],
            ])
            ->add(SystemSettings::ENABLED_CURRENCIES, SameKeyValueType::class, [
                'entry_options' => [
                    'translation_domain' => 'messages',
                    'label_format' => null,
                ],
            ])
            ->add(SystemSettings::ENABLED_COUNTRIES, KeyValueType::class)
            ->add(SystemSettings::ENABLED_LANGUAGES, KeyValueType::class)
        ;
    }
}
