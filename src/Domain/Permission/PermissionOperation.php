<?php

declare(strict_types=1);

namespace App\Domain\Permission;

enum PermissionOperation: string
{
    case READ = 'READ';
    case INSERT = 'INSERT';
    case MODIFY = 'MODIFY';
    case DELETE = 'DELETE';
    case APPROVE = 'APPROVE';
}
