<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Domain\Document\Company\Company;
use App\Http\Controller\QuickSearchController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provides a way to efficiently list companies in a select field - using autocomplete.
 *
 * @see CompanyAutocompleteType
 * @see QuickSearchController
 */
class CompanyAutocompleteType extends AbstractType
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Company::class,
            'autocomplete_url' => $this->urlGenerator->generate('quick_search_companies'),
        ]);
    }

    public function getParent(): string
    {
        return AutocompleteDocumentType::class;
    }
}
