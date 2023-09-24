<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security\Voter;

use App\Application\Security\Permissions\ComplianceCase;
use App\Application\Security\Voter\ComplianceCaseVoter;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ComplianceCaseVoterTest extends UnitTestCase
{
    private ComplianceCaseVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new ComplianceCaseVoter();

        parent::setUp();
    }

    public function testItDeniesAccessToInvalidUser(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($this->createStub(UserInterface::class));
        self::assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, $this->createStub(PayoutBlock::class), ComplianceCase::getConstants()));
    }

    public function testItGrantsAccessInOpenStatusWhenUserHasSufficientPermissions(): void
    {
        $case = new PayoutBlock();
        $user = new Administrator();

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->getToken($user),
                $this->getOpenCase($case),
                [ComplianceCase::ASSIGN_REVIEW]
            )
        );
    }

    public function testItDeniesAccessInOpenStatusWhenUserDoesntHaveSufficientPermissions(): void
    {
        $case = new PayoutBlock();
        $user = new Administrator();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->getToken($user),
                $this->getInReviewCase($case, $user),
                [ComplianceCase::ASSIGN_REVIEW]
            )
        );
    }

    public function testItGrantsAccessInReviewStatusWhenUserHasSufficientPermissions(): void
    {
        $case = new PayoutBlock();
        $user = new Administrator();

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->getToken($user),
                $this->getInReviewCase($case, $user),
                [ComplianceCase::REVIEW]
            )
        );

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->getToken($user),
                $this->getInReviewCase($case, new Administrator()),
                [ComplianceCase::ASSUME_REVIEW]
            )
        );
    }

    public function testItDeniesAccessInReviewStatusWhenUserDoesntHaveSufficientPermissions(): void
    {
        $case = new PayoutBlock();
        $user = new Administrator();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->getToken($user),
                $case,
                [ComplianceCase::REVIEW]
            )
        );

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->getToken($user),
                $case,
                [ComplianceCase::ASSUME_REVIEW]
            )
        );
    }

    public function testItGrantsAccessInApprovalStatusWhenUserHasSufficientPermissions(): void
    {
        $case = new PayoutBlock();
        $user = new Administrator();

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->getToken($user),
                $this->getInApprovalAssignedCase($case, $user),
                [ComplianceCase::APPROVE]
            )
        );

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->getToken($user),
                $this->getInApprovalAssignedCase($case, new Administrator()),
                [ComplianceCase::ASSUME_APPROVE]
            )
        );

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->getToken($user),
                $this->getInApprovalUnAssignedCase($case, $user),
                [ComplianceCase::ASSIGN_APPROVE]
            )
        );
    }

    public function testItDeniesAccessInApprovalStatusWhenUserDoesntHaveSufficientPermissions(): void
    {
        $case = new PayoutBlock();
        $user = new Administrator();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->getToken($user),
                $case,
                [ComplianceCase::APPROVE]
            )
        );

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->getToken($user),
                $case,
                [ComplianceCase::ASSUME_APPROVE]
            )
        );

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->getToken($user),
                $case,
                [ComplianceCase::ASSIGN_APPROVE]
            )
        );
    }

    private function getOpenCase(PayoutBlock $case): PayoutBlock
    {
        $case->setReviewed(false);
        $case->setApproved(false);
        $case->setHandler(null);

        return $case;
    }

    private function getInReviewCase(PayoutBlock $case, Administrator $user): PayoutBlock
    {
        $case->setReviewed(false);
        $case->setHandler($user);
        $case->setApprover(null);

        return $case;
    }

    private function getInApprovalAssignedCase(PayoutBlock $case, Administrator $user): PayoutBlock
    {
        $case->setReviewed(true);
        $case->setApproved(false);
        $case->setApprover($user);

        return $case;
    }

    private function getInApprovalUnAssignedCase(PayoutBlock $case, Administrator $user): PayoutBlock
    {
        $case->setReviewed(true);
        $case->setApproved(false);
        $case->setApprover(null);

        return $case;
    }

    private function getToken(Administrator $user): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
