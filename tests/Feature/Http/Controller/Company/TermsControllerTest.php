<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Infrastructure\Repository\Company\CompanyTermRepository;
use App\Tests\Feature\BaseTestCase;

class TermsControllerTest extends BaseTestCase
{
    public function testItWillNotGetPreviousTermsWhenFormIsSubmitted(): void
    {
        $testCompany = $this->getTestCompany();
        $companyTermRepository = self::getContainer()->get(CompanyTermRepository::class);

        self::$client->request('GET', sprintf('/merchants/%s/terms/create', $testCompany->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Update Terms');

        self::$client->submitForm('Submit', [
            'term' => [
                'termsAndConditions' => 'should return this',
            ],
        ]);

        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        $newTerm = $companyTermRepository->getNewTerms($testCompany);
        self::assertNotSame('Should return this', $newTerm->getTermsAndConditions());
    }
}
