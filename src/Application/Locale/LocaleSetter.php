<?php

declare(strict_types=1);

namespace App\Application\Locale;

use Symfony\Component\HttpFoundation\Request;

class LocaleSetter
{
    final public const LOCALE_ATTRIBUTE = '_locale';

    public function __construct(private readonly LocaleNegotiator $localeNegotiator)
    {
    }

    public function setLocale(Request $request, ?string $locale = null): void
    {
        if (!$request->hasPreviousSession()) {
            return;
        }

        $session = $request->getSession();

        if (null !== $locale) {
            $session->set(self::LOCALE_ATTRIBUTE, $locale);
        }

        // try to see if the locale has been set as a _locale routing parameter
        $locale = $request->attributes->get(self::LOCALE_ATTRIBUTE, $session->get(self::LOCALE_ATTRIBUTE));
        $locale = $this->localeNegotiator->negotiateLocale($locale);
        $session->set(self::LOCALE_ATTRIBUTE, $locale);
        $request->setLocale($locale);
    }
}
