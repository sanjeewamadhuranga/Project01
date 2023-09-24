<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Configuration;

use App\Application\Security\Permissions\Permission;
use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Validator\ManagerPortalRolePermission;
use App\Tests\Feature\BaseTestCase;
use Symfony\Component\DomCrawler\Crawler;

class ManagerRolesControllerTest extends BaseTestCase
{
    private const EDIT_ROLE_ROUTE = '/configuration/manager-roles/%s/edit';

    public function testItRedirectsToIndexWhenUserHasNoRightsToEditRole(): void
    {
        $this->authenticate($this->getUser());

        self::$client->request('GET', sprintf(self::EDIT_ROLE_ROUTE, $this->getProtectedRole()->getId()));
        self::$client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.alert.alert-danger');
    }

    public function testItDisplaysFormWhenUserHasRoleWhichProtects(): void
    {
        $this->authenticate($this->getUserWithProtectedRole());

        self::$client->request('GET', sprintf(self::EDIT_ROLE_ROUTE, $this->getProtectedRole()->getId()));

        self::assertResponseIsSuccessful();
    }

    public function testItFailsWhenTryingToAddAdministratorPermissionsWhenUserDoNotHaveAllPermissions(): void
    {
        $this->authenticate($this->getUserWithProtectedRole());

        $protectedPermission = 'administrators.*';
        $result = $this->sendFormWithProtectedPermission($protectedPermission);

        self::assertStringContainsString(sprintf((new ManagerPortalRolePermission())->message, $protectedPermission), $result->html());
    }

    public function testItSuccessfulAddsAdministratorPermissionWhenUserHaveAllPermissions(): void
    {
        $this->sendFormWithProtectedPermission();
        $result = self::$client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Successfully updated!', $result->html());
    }

    private function sendFormWithProtectedPermission(string $protectedPermission = 'administrators.*'): Crawler
    {
        self::$client->request('GET', sprintf('/configuration/manager-roles/%s/edit', $this->getProtectedRole()->getId()));
        self::assertResponseIsSuccessful();

        return self::$client->submitForm('Submit', [
            'manager_role' => [
                'newPermissions' => array_filter(Permission::getAllPermissions(), fn (string $value) => $protectedPermission === $value),
            ],
        ]);
    }

    private function getUser(): Administrator
    {
        return static::$fixtures['user_roles_admin']; // @phpstan-ignore-line
    }

    private function getUserWithProtectedRole(): Administrator
    {
        return static::$fixtures['user_with_protected_role']; // @phpstan-ignore-line
    }

    private function getProtectedRole(): ManagerPortalRole
    {
        return static::$fixtures['ROLE_PROTECTED_ROLE']; // @phpstan-ignore-line
    }
}
