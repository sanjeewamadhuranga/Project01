<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration;

use App\Domain\Document\Configuration\Bank;
use App\Infrastructure\Form\Type\EnabledCountryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bankCode', TextType::class, [
                'label' => 'bank.formLabels.bankCode',
            ])
            ->add('bankName', TextType::class, [
                'label' => 'bank.formLabels.bankName',
            ])
            ->add('country', EnabledCountryType::class, [
                'label' => 'merchant.users.address.country',
                'required' => false,
            ])
            ->add('branches', CollectionType::class, [
                'entry_type' => BankBranchType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Bank::class);
    }
}
