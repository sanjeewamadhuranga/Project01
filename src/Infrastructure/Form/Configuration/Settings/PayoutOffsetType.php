<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Settings;

use App\Domain\Settings\SystemSettings;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class PayoutOffsetType extends BaseSystemSettingType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(SystemSettings::PAYOUT_OFFSET, NumberType::class, [
                'html5' => true,
                'attr' => ['min' => 0],
                'constraints' => [new PositiveOrZero()],
            ])
            ->get(SystemSettings::PAYOUT_OFFSET)
            ->addModelTransformer(
                new CallbackTransformer(
                    fn ($value) => null !== $value ? (int) $value : null,
                    fn ($value) => (null !== $value ? (string) $value : null)
                )
            )
        ;
    }
}
