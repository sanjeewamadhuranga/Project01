<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\Security\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function loadUserByIdentifier(string $identifier): Administrator
    {
        $user = $this->userRepository->getUserByEmail($identifier);

        if (!$user instanceof Administrator || $user->isDeleted()) {
            $exception = new UserNotFoundException(sprintf("User '%s' not found.", $identifier));
            $exception->setUserIdentifier($identifier);

            throw $exception;
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): Administrator
    {
        if (!$user instanceof Administrator) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return Administrator::class === $class || is_subclass_of($class, Administrator::class);
    }

    public function upgradePassword(UserInterface|PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Administrator) {
            return;
        }

        $user->setPassword($newHashedPassword);

        $this->userRepository->getDocumentManager()->flush();
    }
}
