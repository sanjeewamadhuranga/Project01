<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ManagerPortalRolePermissionValidator extends ConstraintValidator
{
    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker, private readonly DocumentManager $documentManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ManagerPortalRolePermission) {
            throw new UnexpectedTypeException($constraint, ManagerPortalRolePermission::class);
        }

        if ($this->authorizationChecker->isGranted('all_permissions')) {
            return;
        }

        $newPermissions = $value->getNewPermissions();
        $oldPermissions = $this->documentManager->getUnitOfWork()->getOriginalDocumentData($value)['newPermissions'] ?? [];

        $removedPermissions = array_diff($oldPermissions, $newPermissions);
        $addedPermissions = array_diff($newPermissions, $oldPermissions);
        $editedPermissions = array_merge($removedPermissions, $addedPermissions);

        foreach ($editedPermissions as $editedPermission) {
            if (!$this->authorizationChecker->isGranted('EDIT_ROLE_PERMISSIONS', $editedPermission)) {
                $this->context->buildViolation(sprintf($constraint->message, $editedPermission))->addViolation();
            }
        }
    }
}
