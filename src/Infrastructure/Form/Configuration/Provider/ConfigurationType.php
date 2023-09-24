<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Provider;

use App\Domain\Document\Provider\Provider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', TextType::class, [
                'label' => 'config_provider.label.value',
            ])
            ->add('userAgentHeaderString', TextType::class, [
                'label' => 'config_provider.label.userAgentHeaderString',
                'required' => false,
            ])
            ->add('buyerIdentifierSupport', CheckboxType::class, [
                'label' => 'config_provider.label.buyerIdentifierSupport',
                'required' => false,
            ])
            ->add('unified', CheckboxType::class, [
                'label' => 'config_provider.label.unified',
                'required' => false,
            ])
            ->add('announceToProvider', CheckboxType::class, [
                'label' => 'config_provider.label.announceToProvider',
                'required' => false,
            ])
            ->add('canReannounceToProvider', CheckboxType::class, [
                'label' => 'config_provider.label.reAnnounceToProvider',
                'required' => false,
            ])->add('wallet', CheckboxType::class, [
                'label' => 'config_provider.label.wallet',
                'required' => false,
            ])
            ->add('ecommerce', CheckboxType::class, [
                'label' => 'config_provider.label.ecommerce',
                'required' => false,
            ])
            ->add('mobile', CheckboxType::class, [
                'label' => 'config_provider.label.mobile',
                'required' => false,
            ])
            ->add('announceWalletScanToProvider', CheckboxType::class, [
                'label' => 'config_provider.label.announceCustomerPresent',
                'required' => false,
            ])
            ->add('preSelectScanMode', CheckboxType::class, [
                'label' => 'config_provider.label.preSelectScanMode',
                'required' => false,
            ])
            ->add('preauth', CheckboxType::class, [
                'label' => 'config_provider.label.preauth',
                'required' => false,
            ])
            ->add('void', CheckboxType::class, [
                'label' => 'config_provider.label.paymentVoid',
                'required' => false,
            ])
            ->add('providerSettlementTime', TextType::class, [
                'label' => 'config_provider.label.providerSettlementTime',
                'required' => false,
            ])
            ->add('recurringBillingSupport', CheckboxType::class, [
                'label' => 'config_provider.label.recurringBillingSupport',
                'required' => false,
            ])
            ->add('nativeQrCodeSupport', CheckboxType::class, [
                'label' => 'config_provider.label.nativeQrCodeSupport',
                'required' => false,
            ])
            ->add('tokenizedPayments', CheckboxType::class, [
                'label' => 'config_provider.label.tokenizedPayments',
                'required' => false,
            ])
            ->add('tokenizedRecurringPayments', CheckboxType::class, [
                'label' => 'config_provider.label.tokenizedRecurringPayments',
                'required' => false,
            ])
            ->add('virtualProvider', CheckboxType::class, [
                'label' => 'config_provider.label.virtualProvider',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Provider::class);
        $resolver->setDefault('inherit_data', true);
    }
}
