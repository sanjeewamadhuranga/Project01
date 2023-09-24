<?php

declare(strict_types=1);

namespace App\Application\Locale;

/**
 * Matches one of the supported locales with the locale requested by user.
 * Falls back to default locale if none is matched.
 */
class LocaleNegotiator
{
    /**
     * @param string[] $availableLocales
     */
    public function __construct(private readonly string $defaultLocale, private readonly array $availableLocales)
    {
    }

    public function negotiateLocale(?string $preferredLocale): string
    {
        if (null === $preferredLocale) {
            return $this->defaultLocale;
        }

        if (in_array($preferredLocale, $this->availableLocales, true)) {
            return $preferredLocale;
        }

        foreach ($this->availableLocales as $availableLocale) {
            if (2 === strlen($preferredLocale) && str_starts_with($availableLocale, $preferredLocale)) {
                return $availableLocale;
            }
        }

        return $this->defaultLocale;
    }
}
