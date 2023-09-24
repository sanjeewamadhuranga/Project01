<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Configuration\BankBranch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankBranchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('branchCode', TextType::class, [
                'label' => 'bankBranch.formLabels.branchCode',
            ])
            ->add('branchName', TextType::class, [
                'label' => 'bankBranch.formLabels.branchName',
            ])
            ->add('city', TextType::class, [
                'label' => 'merchant.address.city',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', BankBranch::class);
    }
}
