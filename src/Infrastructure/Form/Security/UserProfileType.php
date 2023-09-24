<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Security;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Form\Type\LanguageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('locale', LanguageType::class, [
                'label' => 'administrators.label.language',
                'lowercase' => true,
            ])
            ->add(
                $builder->create('layout', FormType::class, [
                    'inherit_data' => true,
                    'label' => 'administrators.label.layout',
                ])
                    ->add('condensedLayout', ChoiceType::class, [
                        'label' => 'administrators.label.tableDensity',
                        'choices' => [
                            'administrators.label.comfortableLayout' => false,
                            'administrators.label.condensedLayout' => true,
                        ],
                        'multiple' => false,
                        'expanded' => true,
                    ])
                    ->add('fluidLayout', ChoiceType::class, [
                        'label' => 'administrators.label.pageWidth',
                        'choices' => [
                            'administrators.label.fluidLayout' => true,
                            'administrators.label.fixedLayout' => false,
                        ],
                        'multiple' => false,
                        'expanded' => true,
                    ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Administrator::class);
    }
}
