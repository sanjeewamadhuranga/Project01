<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Compliance;

use App\Domain\Compliance\PayoutBlockReason;
use App\Domain\Compliance\PayoutBlockStatus;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\DataGrid\Compliance\CaseList;
use App\Infrastructure\Repository\Company\CompanyRepository;
use App\Infrastructure\Repository\PayoutBlockRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CaseListTest extends UnitTestCase
{
    public function testItTransformsPayoutBlockIntoArray(): void
    {
        $companyId = 'company-id';
        $companyName = 'company registered name';

        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn($companyId);
        $company->method('getRegisteredName')->willReturn($companyName);

        $handlerEmail = 'handler@pay.com';
        $handler = $this->createStub(Administrator::class);
        $handler->method('getEmail')->willReturn($handlerEmail);

        $approverEmail = 'approver@pay.com';
        $approver = $this->createStub(Administrator::class);
        $approver->method('getEmail')->willReturn($approverEmail);

        $id = '61f021a163b571290822cdd9';
        $createdAt = new DateTime();
        $reason = PayoutBlockReason::AMOUNT_DUPLICATE;
        $approved = false;
        $reviewed = true;
        $status = PayoutBlockStatus::IN_REVIEW;
        $email = 'payout-block@pay.com';

        $payoutBlock = $this->createStub(PayoutBlock::class);
        $payoutBlock->method('getCompany')->willReturn($company);
        $payoutBlock->method('getApprover')->willReturn($approver);
        $payoutBlock->method('getHandler')->willReturn($handler);
        $payoutBlock->method('getId')->willReturn($id);
        $payoutBlock->method('getCreatedAt')->willReturn($createdAt);
        $payoutBlock->method('getReason')->willReturn($reason);
        $payoutBlock->method('isApproved')->willReturn($approved);
        $payoutBlock->method('isReviewed')->willReturn($reviewed);
        $payoutBlock->method('getStatus')->willReturn($status);
        $payoutBlock->method('getEmail')->willReturn($email);

        $payoutBlockList = new CaseList($this->createStub(PayoutBlockRepository::class), $this->createStub(CompanyRepository::class), $this->createStub(TokenStorageInterface::class));

        self::assertSame([
            'id' => $id,
            'company' => [
                'id' => $companyId,
                'name' => $companyName,
                'riskProfile' => null,
                'riskProfileId' => null,
            ],
            'createdAt' => $createdAt,
            'reason' => $reason,
            'approved' => $approved,
            'reviewed' => $reviewed,
            'approver' => $approverEmail,
            'handler' => $handlerEmail,
            'status' => $status,
            'email' => $email,
        ], $payoutBlockList->transform($payoutBlock, 0));
    }
}
