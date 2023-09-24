<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Infrastructure\Repository\CountryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\Loader\IntlCallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CountryType as BaseCountryType;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extends Symfony's {@see BaseCountryType} and limits the list of countries to the ones in database.
 * Country names are translated thanks to the base type that provides country names.
 */
class EnabledCountryType extends AbstractType
{
    public function __construct(private readonly CountryRepository $repository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => function (Options $options): ChoiceLoaderInterface {
                $locale = $options['choice_translation_locale'];
                /** @var bool $alpha3 */
                $alpha3 = $options['alpha3'];

                return ChoiceList::loader($this, new IntlCallbackChoiceLoader(function () use ($locale, $alpha3): array {
                    $enabledCountries = [];
                    $intlCountries = $alpha3 ? Countries::getAlpha3Names($locale) : Countries::getNames($locale);
                    foreach ($this->repository->findAll() as $country) {
                        $name = $intlCountries[$country->getCountryCode()] ?? $country->getCountryName();
                        $enabledCountries[$name] = $alpha3 ? $country->getCountryISO3Code() : $country->getCountryCode();
                    }

                    return $enabledCountries;
                }));
            },
            'placeholder' => 'merchant.select_one',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return BaseCountryType::class;
    }
}
