<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Role\Permission;
use App\Domain\Permission\Module;
use App\Domain\Permission\PermissionOperation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('module', EnumType::class, [
                'class' => Module::class,
                'choice_translation_domain' => false,
                'attr' => [
                    'class' => 'form-select-sm',
                ],
            ])
            ->add('allowedOperations', EnumType::class, [
                'class' => PermissionOperation::class,
                'choice_translation_domain' => false,
                'multiple' => true,
                'expanded' => true,
                'label_attr' => [
                    'class' => 'checkbox-switch checkbox-inline mb-0',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permission::class,
            'translation_domain' => 'configuration',
        ]);
    }
}
