<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Company\PushNotificationType;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Role\Role;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'required' => true,
            ])
            ->add('companies', DocumentType::class, [
                'required' => false,
                'class' => Company::class,
                'choice_label' => 'tradingName',
                'attr' => ['class' => 'tom-select'],
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('allowedPushNotifications', EnumType::class, [
                'class' => PushNotificationType::class,
                'choice_label' => 'readable',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('permissions', CollectionType::class, [
                'entry_type' => PermissionType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('userAware', CheckboxType::class, [
                'required' => false,
            ])
            ->add('default', CheckboxType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}
