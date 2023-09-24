<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Company\ReviewStatus;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Remittance;
use App\Domain\Transaction\RemittanceStatus;
use App\Infrastructure\DataGrid\RemittanceList;
use App\Infrastructure\Repository\RemittanceRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;

class RemittanceListTest extends UnitTestCase
{
    public function testItTransformsRemittanceIntoArray(): void
    {
        $companyId = 'company-id-abj5';
        $companyName = 'some-another-company-name';
        $companyReviewStatus = ReviewStatus::PENDING;

        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn($companyId);
        $company->method('getReviewStatus')->willReturn($companyReviewStatus);
        $company->method('__toString')->willReturn($companyName);

        $id = '61f021dc44ade122460c47af';
        $amount = 90000;
        $currency = 'USD';
        $status = RemittanceStatus::CONFIRMED;
        $createdAt = new DateTime();
        $externalId = 'test-external-id';
        $isPaid = false;
        $description = 'test-description';

        $remittance = $this->createStub(Remittance::class);
        $remittance->method('getId')->willReturn($id);
        $remittance->method('getAmount')->willReturn($amount);
        $remittance->method('getCurrency')->willReturn($currency);
        $remittance->method('getState')->willReturn($status);
        $remittance->method('getCreatedAt')->willReturn($createdAt);
        $remittance->method('getExternalId')->willReturn($externalId);
        $remittance->method('isPaid')->willReturn($isPaid);
        $remittance->method('getMerchant')->willReturn($company);
        $remittance->method('getDescription')->willReturn($description);

        $remittanceList = new RemittanceList($this->createStub(RemittanceRepository::class));

        self::assertSame([
            'id' => $id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'createdAt' => $createdAt,
            'companyId' => $companyId,
            'tradingName' => $companyName,
            'externalId' => $externalId,
            'description' => $description,
            'paid' => $isPaid,
            'reviewStatus' => $companyReviewStatus,
        ], $remittanceList->transform($remittance, 0));
    }
}
