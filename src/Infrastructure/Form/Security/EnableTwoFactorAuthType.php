<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Security;

use App\Http\Model\Request\EnableTwoFactorRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnableTwoFactorAuthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'enterCode',
                'attr' => ['autocomplete' => 'one-time-code'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'verify',
                'attr' => [
                    'class' => 'w-100 btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnableTwoFactorRequest::class,
        ]);
    }
}
