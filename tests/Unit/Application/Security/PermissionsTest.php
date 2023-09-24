<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security;

use App\Application\Security\Permissions\OldPermission;
use App\Application\Security\Permissions\Permission;
use App\Tests\Unit\UnitTestCase;

class PermissionsTest extends UnitTestCase
{
    public function testMappedPermissionsExitInNewPermissions(): void
    {
        foreach (OldPermission::permissionMap() as $mapPermissions) {
            foreach ((array) $mapPermissions as $newPermission) {
                self::assertContains($newPermission, Permission::getAllPermissions(), $newPermission.' does not exit in Permission::getAllPermissions();');
            }
        }
    }

    public function testAllNewPermissionAreMappedToNewOnes(): void
    {
        foreach (OldPermission::getConstants() as $permission) {
            self::assertContains($permission, array_keys(OldPermission::permissionMap()), $permission.' does not exit in OldPermissions::mapPermissions();');
        }
    }

    public function testItAllNewPermissionsEndWithADot(): void
    {
        foreach (Permission::getConstants() as $permission) {
            self::assertStringEndsWith('.', $permission);
        }
    }
}
