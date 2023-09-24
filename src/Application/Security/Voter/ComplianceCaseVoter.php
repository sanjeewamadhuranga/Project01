<?php

declare(strict_types=1);

namespace App\Application\Security\Voter;

use App\Application\Security\Permissions\ComplianceCase;
use App\Domain\Compliance\PayoutBlockStatus;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Security\Administrator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ComplianceCaseVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, ComplianceCase::getConstants(), true)) {
            return false;
        }

        if (!$subject instanceof PayoutBlock) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Administrator) {
            return false;
        }

        return match ($attribute) {
            ComplianceCase::ASSUME_REVIEW => $this->canAssumeReview($subject, $user),
            ComplianceCase::ASSUME_APPROVE => $this->canAssumeApprove($subject, $user),
            ComplianceCase::ASSIGN_REVIEW => $this->canAssignReview($subject),
            ComplianceCase::ASSIGN_APPROVE => $this->canAssignApprove($subject),
            ComplianceCase::REVIEW => $this->canReview($subject, $user),
            ComplianceCase::APPROVE => $this->canApprove($subject, $user),
            default => false,
        };
    }

    private function canAssumeReview(PayoutBlock $case, Administrator $user): bool
    {
        if ($case->getApprover() === $user || $case->getHandler() === $user) {
            return false;
        }

        return PayoutBlockStatus::IN_REVIEW === $case->getStatus() && null !== $case->getHandler();
    }

    private function canAssumeApprove(PayoutBlock $case, Administrator $user): bool
    {
        if ($case->getHandler() === $user || $case->getApprover() === $user) {
            return false;
        }

        return PayoutBlockStatus::IN_APPROVAL === $case->getStatus() && null !== $case->getApprover();
    }

    private function canAssignReview(PayoutBlock $case): bool
    {
        return in_array($case->getStatus(), [PayoutBlockStatus::OPEN, PayoutBlockStatus::IN_REVIEW], true)
            && null === $case->getHandler();
    }

    private function canAssignApprove(PayoutBlock $case): bool
    {
        return PayoutBlockStatus::IN_APPROVAL === $case->getStatus() && null === $case->getApprover();
    }

    private function canReview(PayoutBlock $case, Administrator $user): bool
    {
        if ($case->getApprover() === $user) {
            return false;
        }

        return PayoutBlockStatus::IN_REVIEW === $case->getStatus() && $case->getHandler() === $user;
    }

    private function canApprove(PayoutBlock $case, Administrator $user): bool
    {
        if ($case->getHandler() === $user) {
            return false;
        }

        return PayoutBlockStatus::IN_APPROVAL === $case->getStatus() && $case->getApprover() === $user;
    }
}
