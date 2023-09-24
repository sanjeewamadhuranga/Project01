<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Document\Security\Administrator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserUpdater
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function updateUser(Administrator $user): void
    {
        $user->setEmailCanonical($this->canonicalize($user->getEmail()));
        $user->setUsername($this->canonicalize($user->getEmail()));
        $user->setUsernameCanonical($this->canonicalize($user->getUserIdentifier()));
        $plainPassword = $user->getPlainPassword();

        if (null !== $plainPassword) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
            $user->eraseCredentials();
        }
    }

    private function canonicalize(string $string): string
    {
        return mb_strtolower($string);
    }
}
