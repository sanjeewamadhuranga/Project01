<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Settings;

use App\Domain\Company\Status;
use App\Domain\Settings\FederatedIdentityType;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\BooleanStringType;
use App\Infrastructure\Form\Type\EnabledCurrencyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class BrandingType extends BaseSystemSettingType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(SystemSettings::OWNER, TextType::class)
            ->add(SystemSettings::ADMIN_THEME, TextType::class)
            ->add(SystemSettings::DASHBOARD, UrlType::class)
            ->add(SystemSettings::API_DOMAIN, UrlType::class)
            ->add(SystemSettings::FEDERATED_ID_PASSWORDLESS_LOGIN, BooleanStringType::class)
            ->add(SystemSettings::FEDERATED_ID_PRIMARY, ChoiceType::class, [
                'choices' => array_flip(FederatedIdentityType::readables()),
                'choice_translation_domain' => 'messages',
            ])
            ->add(SystemSettings::ENABLED_CUSTOM_ACCOUNT_FIELDS, ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label_attr' => ['class' => 'checkbox-switch'],
                'choices' => [
                    'value1' => 'value1',
                    'value2' => 'value2',
                    'value3' => 'value3',
                    'value4' => 'value4',
                ],
                'choice_translation_domain' => false,
            ])
            ->add(SystemSettings::DEFAULT_COMPANY_REVIEW_STATUS, ChoiceType::class, [
                'choices' => array_flip(Status::readables()),
                'choice_translation_domain' => 'messages',
                'empty_data' => Status::PENDING->value,
            ])
            ->add(SystemSettings::BASE_CURRENCY, EnabledCurrencyType::class)
            ->add(SystemSettings::RISK_PROFILES_SUPPORTED_CURRENCIES, EnabledCurrencyType::class, [
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'tom-select'],
            ])
        ;
    }
}
