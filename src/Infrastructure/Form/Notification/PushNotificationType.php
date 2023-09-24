<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Notification;

use App\Domain\Document\Notification\PushNotification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PushNotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('headings', TextType::class, [
                'label' => 'merchant.pushNotification.heading',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'merchant.pushNotification.message',
                'required' => true,
            ])
            ->add('link', TextType::class, [
                'label' => 'merchant.pushNotification.link',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-secondary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', PushNotification::class);
    }
}
