<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\Repository;

use App\Domain\Document\Term\CompanyTerm;
use App\Infrastructure\Repository\Company\CompanyTermRepository;
use App\Tests\Feature\BaseTestCase;

class CompanyTermsRepositoryTest extends BaseTestCase
{
    private CompanyTermRepository $companyTermRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyTermRepository = self::getContainer()->get(CompanyTermRepository::class);
    }

    public function testItCreateNewTermFromPreviousCompanyTerm(): void
    {
        $testTerm = $this->companyTermRepository->getNewTerms($this->refresh($this->getTestCompany()));

        /** @var CompanyTerm $latestTerm */
        $latestTerm = $this->refresh(self::$fixtures['companyTerm_latest']); // @phpstan-ignore-line
        self::assertSame($latestTerm->getTermsAndConditions(), $testTerm->getTermsAndConditions());
        self::assertSame($latestTerm->getCompany(), $testTerm->getCompany());
        self::assertNotSame($latestTerm, $testTerm);
    }
}
