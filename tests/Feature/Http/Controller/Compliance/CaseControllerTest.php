<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Compliance;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\PayoutBlockRepository;
use App\Tests\Feature\BaseTestCase;

class CaseControllerTest extends BaseTestCase
{
    private Administrator $handler;

    private Administrator $approver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTouchesDb();

        $this->handler = $this->getTestUserForReview();
        $this->approver = $this->getTestUserForApproval();
    }

    /**
     * @group smoke
     */
    public function testItShowsCaseList(): void
    {
        self::$client->request('GET', '/compliance/case');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('compliance-case-list');
    }

    public function testListCases(): void
    {
        self::$client->request('GET', '/compliance/case/list');
        self::assertResponseIsSuccessful();
        $this->assertGridResponse();
    }

    public function testItCreatesCase(): void
    {
        /** @var Company $company */
        $company = self::$fixtures['test_company_1'];

        self::$client->request('GET', sprintf('/compliance/case/create'));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Create Case');

        // Disable validation for AutocompleteDocumentType.
        $form = self::$client->getCrawler()->selectButton('Submit')->form()->disableValidation();
        self::$client->submit($form, [
            'payout_block' => [
                'company' => $company->getId(),
                'reason' => 'compliance.amount.daily',
                'comments' => 'abc 123',
            ],
        ]);

        $case = self::getContainer()->get(PayoutBlockRepository::class)->findOneBy([
            'reason' => 'compliance.amount.daily',
            'company' => $company->getId(),
        ]);
        self::assertInstanceOf(PayoutBlock::class, $case);
    }

    public function testTheCaseFlowFromOpenListAssignReview(): void
    {
        $case = $this->getTestOpenCase();

        // assign review case to a handler by another user
        $this->authenticate();
        self::$client->request('GET', sprintf('/compliance/case/%s', $case->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Case Detail');

        self::$client->submitForm('Assign reviewer', [
            'case_assign' => [
                'assignTo' => 'user',
                'handler' => $this->handler->getId(),
            ],
        ]);
        $this->validateCase($case, $this->handler->getId());

        // review case by assigned handler
        $this->authenticate($this->handler);
        $this->reviewAndSubmit($case, $this->approver->getId());
        $this->validateCase($case, $this->handler->getId(), $this->approver->getId(), true);

        // approve case by assigned approver
        $this->authenticate($this->approver);
        $this->approveAndSubmit($case);
        $this->validateCase($case, $this->handler->getId(), $this->approver->getId(), true, true);
    }

    public function testTheCaseFlowFromInReviewListAssumeReview(): void
    {
        $case = $this->getTestInReviewCase();

        // assume review case to self by handler
        $this->authenticate($this->handler);
        self::$client->request('GET', sprintf('/compliance/case/%s', $case->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Case Detail');

        self::$client->submitForm('Assume reviewer', [
            'case_assign' => [],
        ]);
        $this->validateCase($case, $this->handler->getId());

        // review case by assigned handler
        $this->reviewAndSubmit($case, $this->approver->getId());
        $this->validateCase($case, $this->handler->getId(), $this->approver->getId(), true);

        // approve case by assigned approver
        $this->authenticate($this->approver);
        $this->approveAndSubmit($case);
        $this->validateCase($case, $this->handler->getId(), $this->approver->getId(), true, true);
    }

    public function testTheCaseFlowFromInApprovalListAssignApprove(): void
    {
        $case = $this->getTestInApprovalUnAssignedCase();

        // assume approve case to self by approver
        $this->authenticate();
        self::$client->request('GET', sprintf('/compliance/case/%s', $case->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Case Detail');

        self::$client->submitForm('Assign approval', [
            'case_assign' => [
                'assignTo' => 'user',
                'approver' => $this->approver->getId(),
            ],
        ]);
        $this->validateCase($case, $this->handler->getId(), $this->approver->getId(), true);

        // approve case by assigned approver
        $this->authenticate($this->approver);
        $this->approveAndSubmit($case);
        $this->validateCase($case, $this->handler->getId(), $this->approver->getId(), true, true);
    }

    public function testTheCaseFlowFromInApprovalListAssumeApprove(): void
    {
        $case = $this->getTestInApprovalAssignedCase();

        // assign approve case to approver by another user
        $this->authenticate($this->approver);
        self::$client->request('GET', sprintf('/compliance/case/%s', $case->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Case Detail');

        self::$client->submitForm('Assume approval', [
            'case_assign' => [],
        ]);
        $this->validateCase($case, $this->handler->getId(), $this->approver->getId(), true);

        // approve case by assigned approver
        $this->authenticate($this->approver);
        $this->approveAndSubmit($case);
        $this->validateCase($case, $this->handler->getId(), $this->approver->getId(), true, true);
    }

    private function validateCase(PayoutBlock $case, string $handlerId = null, string $approverId = null, bool $reviewed = false, bool $approved = false): void
    {
        $case = self::getContainer()->get(PayoutBlockRepository::class)->findOneBy([
            'id' => $case->getId(),
            'reviewed' => $reviewed,
            'approved' => $approved,
            'handler' => $handlerId,
            'approver' => $approverId,
        ]);
        self::assertInstanceOf(PayoutBlock::class, $case);
    }

    private function reviewAndSubmit(PayoutBlock $case, string $approverId = null): void
    {
        self::$client->request('GET', sprintf('/compliance/case/%s', $case->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Case Detail');

        self::$client->submitForm('Submit for approval', [
            'case_review' => [
                'caseFlow' => [
                    'reviewComments' => 'Test comment',
                    'finaliseReview' => true,
                ],
                'assignTo' => 'assign_to_user',
                'approver' => $approverId,
            ],
        ]);
    }

    private function approveAndSubmit(PayoutBlock $case): void
    {
        self::$client->request('GET', sprintf('/compliance/case/%s', $case->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Case Detail');

        self::$client->submitForm('Submit', [
            'case_approve_flow' => [
                'approveComments' => 'Test comment',
                'approved' => true,
            ],
        ]);
    }

    protected function getTestUserForReview(): Administrator
    {
        return self::$fixtures['user_ryan']; // @phpstan-ignore-line
    }

    protected function getTestUserForApproval(): Administrator
    {
        return self::$fixtures['user_ion']; // @phpstan-ignore-line
    }

    protected function getTestOpenCase(): PayoutBlock
    {
        return self::$fixtures['case_open']; // @phpstan-ignore-line
    }

    protected function getTestInReviewCase(): PayoutBlock
    {
        return self::$fixtures['case_in_review']; // @phpstan-ignore-line
    }

    protected function getTestInApprovalUnAssignedCase(): PayoutBlock
    {
        return self::$fixtures['case_in_approval_unassigned']; // @phpstan-ignore-line
    }

    protected function getTestInApprovalAssignedCase(): PayoutBlock
    {
        return self::$fixtures['case_in_approval_assigned']; // @phpstan-ignore-line
    }
}
