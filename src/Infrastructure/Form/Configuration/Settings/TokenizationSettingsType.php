<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Settings;

use App\Domain\Settings\SystemSettings;
use App\Domain\Transaction\TokenizationMethod;
use App\Infrastructure\Form\Type\EnumValueType;
use Symfony\Component\Form\FormBuilderInterface;

class TokenizationSettingsType extends BaseSystemSettingType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(SystemSettings::DEFAULT_TOKENIZATION_METHOD, EnumValueType::class, [
            'class' => TokenizationMethod::class,
            'empty_data' => TokenizationMethod::PCI_,
        ]);
    }
}
