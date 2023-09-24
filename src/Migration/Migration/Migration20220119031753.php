<?php

declare(strict_types=1);

namespace App\Migration\Migration;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\OldPermission;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Repository\Security\ManagerPortalRoleRepository;

final class Migration20220119031753 extends AbstractMigration
{
    // Roles that will receive "*" permission instead
    private const ADMIN_ROLES = ['ROLE_SUPER_ADMIN', 'ROLE__ADMIN'];

    public function up(): void
    {
        /** @var ManagerPortalRoleRepository $repo */
        $repo = $this->dm->getRepository(ManagerPortalRole::class);

        foreach ($repo->findAll() as $managerRole) {
            $managerRole->setNewPermissions($this->getNewPermissions($managerRole));
            $this->dm->persist($managerRole);
        }

        $this->dm->flush();
    }

    public function down(): void
    {
        // This is not reversible and it can be run multiple times.
    }

    public function getDescription(): string
    {
        return 'Translates legacy permissions to the new ones. Can be safely rolled back and executed again if needed.';
    }

    /**
     * @return string[]
     */
    private function getNewPermissions(ManagerPortalRole $role): array
    {
        if (in_array($role->getName(), self::ADMIN_ROLES, true)) {
            return [Action::ANY];
        }

        $permissions = $role->getNewPermissions();

        foreach ($role->getPermissions() as $oldPermission) {
            $newPermissions = (array) (OldPermission::permissionMap()[$oldPermission] ?? null);
            foreach ($newPermissions as $newPermission) {
                $permissions[] = $newPermission;
            }
        }

        return array_unique(array_filter($permissions));
    }
}
