<?php

declare(strict_types=1);

namespace App\Application\Security\Voter;

use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProtectedByRoleVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof ManagerPortalRole && 'PROTECT_ROLE_BY_OTHER_ROLE' === $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Administrator) {
            return false;
        }

        $protectedByRole = $subject->getProtectedByRole();

        return null === $protectedByRole || $user->getManagerPortalRoles()->contains($protectedByRole);
    }
}
