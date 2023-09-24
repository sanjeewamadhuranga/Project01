<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Upload;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class AppFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('upload', FileType::class, [
                'label' => 'upload',
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/csv',
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                        ],
                        'maxSize' => '32M',
                    ]),
                ],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-secondary',
                ],
            ])
        ;
    }
}
