<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\HolidayCalendar;

use App\Domain\Document\Configuration\HolidayCalendar\Holiday;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HolidayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'configuration.holiday_calendar.holiday.label.date',
                'widget' => 'single_text',
            ])
            ->add('description', TextType::class, [
                'label' => 'configuration.holiday_calendar.holiday.label.description',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Holiday::class);
    }
}
