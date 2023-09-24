<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Document\Security\Administrator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Administrator) {
            return;
        }

        if ($user->isDeleted()) {
            throw new CustomUserMessageAuthenticationException('User account is deleted!');
        }

        if ($user->isSuspended()) {
            throw new CustomUserMessageAuthenticationException('User account is suspended!');
        }

        if ($user->isExpired()) {
            throw new CustomUserMessageAuthenticationException('User account has expired.');
        }

        if (!$user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);
            throw $ex;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // No checks at post auth.
    }
}
