<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Settings;

use App\Domain\Settings\SystemSettings;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class SetupBrandingType extends BaseSystemSettingType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(SystemSettings::OWNER, TextType::class)
            ->add(SystemSettings::ADMIN_THEME, TextType::class)
            ->add(SystemSettings::DASHBOARD, UrlType::class)
            ->add(SystemSettings::API_DOMAIN, UrlType::class)
            ->add(SystemSettings::MANAGER_PORTAL_URL, UrlType::class)
        ;
    }
}
