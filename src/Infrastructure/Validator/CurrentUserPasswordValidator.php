<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CurrentUserPasswordValidator extends ConstraintValidator
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentUserPassword) {
            throw new UnexpectedTypeException($constraint, CurrentUserPassword::class);
        }

        $user = $this->tokenStorage->getToken()?->getUser();

        if ($user instanceof PasswordAuthenticatedUserInterface
            && $this->userPasswordHasher->isPasswordValid($user, $value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
