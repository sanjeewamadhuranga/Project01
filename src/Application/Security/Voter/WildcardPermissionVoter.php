<?php

declare(strict_types=1);

namespace App\Application\Security\Voter;

use App\Application\Security\Permissions\Action;
use App\Domain\Document\Security\Administrator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WildcardPermissionVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return strtoupper($attribute) !== $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Administrator) {
            return false;
        }

        $attributeRegex = $this->getPattern($attribute);

        foreach ($user->getPermissions() as $permission) {
            if (1 === preg_match($attributeRegex, $permission)) {
                return true;
            }

            if (1 === preg_match($this->getPattern($permission), $attribute)) {
                return true;
            }
        }

        return false;
    }

    private function getPattern(string $permission): string
    {
        return sprintf('#^%s$#i', str_replace(preg_quote(Action::ANY, '#'), '.*', preg_quote($permission, '#')));
    }
}
