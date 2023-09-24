<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Notification;

use App\Domain\Document\Notification\CustomEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EmailNotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'merchant.emailNotification.title',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'merchant.emailNotification.message',
                'required' => true,
                'sanitize_html' => true,
            ])
            ->add('attachmentFile', VichFileType::class, [
                'label' => 'merchant.emailNotification.attachmentFile',
                'required' => false,
            ])
            ->add('send', SubmitType::class, [
                'label' => 'merchant.emailNotification.send',
                'attr' => [
                    'class' => 'btn btn-outline-secondary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CustomEmail::class);
    }
}
