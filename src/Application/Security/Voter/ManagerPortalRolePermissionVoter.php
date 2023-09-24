<?php

declare(strict_types=1);

namespace App\Application\Security\Voter;

use App\Application\Security\Permissions\Permission;
use App\Domain\Document\Security\Administrator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ManagerPortalRolePermissionVoter extends Voter
{
    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return is_string($subject) && 'EDIT_ROLE_PERMISSIONS' === $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Administrator) {
            return false;
        }

        if ($this->authorizationChecker->isGranted('all_permissions')) {
            return true;
        }

        return !in_array($subject, Permission::getAdministratorPermissions(), true);
    }
}
