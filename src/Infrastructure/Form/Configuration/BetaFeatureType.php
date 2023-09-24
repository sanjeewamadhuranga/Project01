<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Addons\BetaFeature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BetaFeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'beta_feature.label.title',
            ])
            ->add('code', TextType::class, [
                'label' => 'beta_feature.label.code',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'beta_feature.label.description',
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'beta_feature.label.logoFile',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', BetaFeature::class);
    }
}
