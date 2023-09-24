<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Settings;

use App\Domain\Company\CompanyDataIntegration;
use App\Domain\Settings\Features;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\BooleanStringType;
use App\Infrastructure\Form\Type\EnumValueType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ManageFeaturesType extends BaseSystemSettingType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $data = $event->getData();

            // Add any features that exist in DB but are not mapped in the code.
            $features = array_unique([...array_values(Features::getConstants()), ...array_values((array) $data[SystemSettings::ENABLED_FEATURES])]);

            sort($features);

            $event->getForm()->add(SystemSettings::ENABLED_FEATURES, ChoiceType::class, [
                'choices' => array_combine($features, $features),
                'multiple' => true,
                'by_reference' => false,
                'expanded' => true,
                'getter' => static fn (array $settings) => array_values($settings[SystemSettings::ENABLED_FEATURES] ?? []),
                'setter' => static fn (array &$settings, ?array $values) => $settings[SystemSettings::ENABLED_FEATURES] = array_values($values ?? []),
                'label_attr' => ['class' => 'checkbox-switch'],
                'priority' => 1,
            ]);
        });

        $this->addSystemSettingsCheckboxes($builder);

        $builder->add(SystemSettings::MERCHANT_DATA_INTEGRATIONS, EnumValueType::class, [
            'class' => CompanyDataIntegration::class,
            'multiple' => true,
            'expanded' => true,
            'label_attr' => ['class' => 'checkbox-switch'],
        ]);
    }

    private function addSystemSettingsCheckboxes(FormBuilderInterface $builder): FormBuilderInterface
    {
        $checkboxSettings = [
            SystemSettings::DISABLE_MANAGER_PORTAL_PASSWORD_LOGIN,
            SystemSettings::SHOW_REGISTRATION_PROVISION,
            SystemSettings::SHOW_OFFERS,
            SystemSettings::SHOW_COMPLIANCE,
            SystemSettings::SHOW_AUTOCREDITS,
            SystemSettings::PAYOUT_REPORT,
            SystemSettings::PLATFORM_BILLING,
            SystemSettings::SHOW_FX_SETTLEMENT_LIST,
            SystemSettings::SHOW_EDC_IMPORT,
            SystemSettings::SHOW_PROVISION_MERCHANT,
        ];

        foreach ($checkboxSettings as $setting) {
            $builder
                ->add($setting, BooleanStringType::class)
            ;
        }

        return $builder;
    }
}
