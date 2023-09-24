<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Provider;

use App\Domain\Document\Provider\Provider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LogoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logoFile', VichImageType::class, [
                'label' => 'config_provider.label.logoFile',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->add('shopBadgeFile', VichImageType::class, [
                'label' => 'config_provider.label.shopBadgeFile',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->add('logoGrayscaleFile', VichImageType::class, [
                'label' => 'config_provider.label.logoGrayScale',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->add('icon', TextType::class, [
                'label' => 'config_provider.label.icon',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Provider::class);
        $resolver->setDefault('inherit_data', true);
    }
}
