<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Domain\Document\Company\ResellerMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HoldCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('holdCode', TextType::class, [
            'label' => 'merchant.financial_update.hold_code.label',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ResellerMetadata::class,
            'choice_translation_domain' => false,
        ]);
    }
}
