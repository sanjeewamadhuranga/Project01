<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Settings;

use App\Domain\Settings\FederatedIdentityType;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\BooleanStringType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class FederatedIdentitySetupType extends BaseSystemSettingType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(SystemSettings::FEDERATED_ID_PRIMARY, ChoiceType::class, [
                'choices' => array_flip(FederatedIdentityType::readables()),
                'choice_translation_domain' => 'messages',
            ])
            ->add(SystemSettings::FEDERATED_ID_PASSWORDLESS_LOGIN, BooleanStringType::class)
        ;
    }
}
