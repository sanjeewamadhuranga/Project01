<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Domain\Document\Security\ManagerPortalRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\PersistentCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ProtectedRoleValidator extends ConstraintValidator
{
    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProtectedRole) {
            throw new UnexpectedTypeException($constraint, ProtectedRole::class);
        }

        if ($value instanceof ArrayCollection) {
            $this->validateEditedRoles($value->getValues(), $constraint);
        }

        if ($value instanceof PersistentCollection) {
            /** @var ManagerPortalRole[] $editedRoles */
            $editedRoles = [...$value->getInsertedDocuments(), ...$value->getDeletedDocuments()];
            $this->validateEditedRoles($editedRoles, $constraint);
        }
    }

    /**
     * @param ManagerPortalRole[] $roles
     */
    private function validateEditedRoles(array $roles, ProtectedRole $constraint): void
    {
        foreach ($roles as $role) {
            if (!$this->authorizationChecker->isGranted('PROTECT_ROLE_BY_OTHER_ROLE', $role)) {
                $this->context->buildViolation(sprintf($constraint->message, $role->getName()))->addViolation();
            }
        }
    }
}
