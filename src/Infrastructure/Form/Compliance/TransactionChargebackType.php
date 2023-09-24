<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Compliance;

use App\Domain\Transaction\MdrAdjustment;
use App\Domain\Transaction\Status;
use App\Domain\Transaction\TransactionCreateRequest;
use App\Domain\Transaction\VendorType;
use App\Infrastructure\Form\Company\AppType;
use App\Infrastructure\Form\Configuration\ProviderSettingType;
use App\Infrastructure\Form\Transaction\AccountingFlagsType;
use App\Infrastructure\Form\Transaction\SideEffectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionChargebackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'required' => false,
                'divisor' => 100,
                'html5' => true,
                'currency' => $builder->getData()->currency,
                'label' => 'transaction_status_change.label.amount',
                'attr' => [
                    'step' => 0.01,
                    'min' => 0.01,
                ],
            ])
            ->add('currency', TextType::class, [
                'label' => 'filters.merchant.currency',
                'disabled' => true,
            ])
            ->add('state', EnumType::class, [
                'label' => 'filters.transaction.state',
                'choice_label' => 'readable',
                'class' => Status::class,
                'choices' => [Status::REFUNDED],
                'disabled' => true,
            ])
            ->add('provider', ProviderSettingType::class, [
                'label' => 'filters.transaction.provider',
                'data' => VendorType::ADJUSTMENT->value,
                'disabled' => true,
            ])
            ->add('merchantDiscountRateCode', EnumType::class, [
                'label' => 'compliance.disputes.label.mdrCode',
                'class' => MdrAdjustment::class,
                'disabled' => true,
                'choice_label' => 'readable',
            ])
            ->add('appId', AppType::class, [
                'label' => 'compliance.disputes.label.addId',
                'company' => $builder->getData()->merchant,
            ])
            ->add('customerReference', TextType::class, [
                'label' => 'transaction_view.label.customerRef',
            ])
            ->add('localId', TextType::class, [
                'label' => 'compliance.disputes.label.localId',
                'required' => false,
            ])
            ->add('sideEffects', SideEffectType::class, [
                'required' => false,
            ])
            ->add('accountingFlags', AccountingFlagsType::class, [
                'required' => false,
            ])
            ->add('adjustmentPurpose', TextareaType::class, [
                'label' => 'compliance.disputes.label.adjustmentPurpose',
            ])
            ->add('responsibilityAcknowledgement', CheckboxType::class, [
                'label' => 'compliance.disputes.label.responsibilityAcknowledgement',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', TransactionCreateRequest::class);
    }
}
