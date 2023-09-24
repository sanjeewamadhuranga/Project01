<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class ManagerPortalRolePermission extends Constraint
{
    public string $message = 'You do not have permission to change permission: %s';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
