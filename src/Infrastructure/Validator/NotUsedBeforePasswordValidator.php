<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Domain\Document\Security\Administrator;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotUsedBeforePasswordValidator extends ConstraintValidator
{
    public function __construct(private readonly PasswordHasherFactoryInterface $passwordHasherFactory, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotUsedBeforePassword) {
            throw new UnexpectedTypeException($constraint, NotUsedBeforePassword::class);
        }

        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof Administrator) {
            return;
        }

        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher($user);
        foreach ($user->getPreviousPasswords() as $previousPassword) {
            if ($passwordHasher->verify($previousPassword, $value)) {
                $this->context->buildViolation($constraint->message)->addViolation();

                return;
            }
        }
    }
}
