<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Integration;
use App\Domain\Integration\Topic;
use App\Domain\Integration\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntegrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'integration.label.name',
            ])
            ->add('type', EnumType::class, [
                'class' => Type::class,
                'choice_translation_domain' => false,
                'label' => 'integration.label.type',
            ])
            ->add('list', TextType::class, [
                'label' => 'integration.label.list',
                'required' => false,
            ])
            ->add('webhook', UrlType::class, [
                'label' => 'integration.label.webhook',
                'required' => false,
            ])
            ->add('webhookEncryption', CheckboxType::class, [
                'label' => 'integration.label.webhookEncryption',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'integration.label.email',
                'required' => false,
            ])
            ->add('topics', EnumType::class, [
                'label' => 'integration.label.topics',
                'class' => Topic::class,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Integration::class);
    }
}
