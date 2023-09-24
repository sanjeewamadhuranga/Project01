<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\Repository;

use App\Domain\Document\Term\TenantTerm;
use App\Infrastructure\Repository\Configuration\TenantTermRepository;
use App\Tests\Feature\BaseTestCase;

class TenantTermRepositoryTest extends BaseTestCase
{
    private TenantTermRepository $tenantTermRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenantTermRepository = self::getContainer()->get(TenantTermRepository::class);
    }

    public function testItCreateNewTermFromPreviousCompanyTerm(): void
    {
        $testTerm = $this->tenantTermRepository->getNewTerms();

        /** @var TenantTerm $latestTerm */
        $latestTerm = $this->refresh(self::$fixtures['tenantTerm_latest']); // @phpstan-ignore-line
        self::assertSame($latestTerm->getTermsAndConditions(), $testTerm->getTermsAndConditions());
        self::assertNotSame($latestTerm, $testTerm);
    }
}
