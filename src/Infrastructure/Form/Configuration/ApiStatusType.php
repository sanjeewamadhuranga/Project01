<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\ApiStatus\MessageType;
use App\Domain\ApiStatus\Platform;
use App\Domain\Document\ApiStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('active', CheckboxType::class, [
                'label' => 'active',
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'class' => MessageType::class,
                'placeholder' => 'merchant.select_one',
                'label' => 'config_status.label.status',
            ])
            ->add('action', TextType::class, [
                'label' => 'config_status.label.url',
            ])
            ->add('title', TextType::class, [
                'label' => 'config_status.label.title',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'config_status.label.message',
            ])
            ->add('apiVersion', TextType::class, [
                'label' => 'config_status.label.apiVersion',
            ])
            ->add('appVersion', TextType::class, [
                'label' => 'config_status.label.appVersion',
            ])
            ->add('platform', EnumType::class, [
                'class' => Platform::class,
                'placeholder' => 'merchant.select_one',
                'label' => 'config_status.label.platform',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', ApiStatus::class);
    }
}
