<?php

declare(strict_types=1);

namespace App\Tests\Feature\Domain\Document\Security;

use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Tests\Feature\BaseTestCase;
use ReflectionClass;

class UserTest extends BaseTestCase
{
    private UserRepository $userRepository;

    private int $maxSavedPasswords;

    protected function setUp(): void
    {
        parent::setUp();

        $this->markTouchesDb();

        $this->userRepository = self::getContainer()->get(UserRepository::class);

        $reflectionClass = new ReflectionClass(Administrator::class);
        $this->maxSavedPasswords = $reflectionClass->getConstant('NUMBER_OF_PREVIOUS_PASSWORDS_REMEMBERED');
    }

    public function testItSavesLastPreviousPasswordsInNumberDeclaredInClass(): void
    {
        $user = new Administrator();
        $user->setPassword('first');
        $previousPasswords = [];

        for ($i = 1; $i < $this->maxSavedPasswords + 10; ++$i) {
            $previousPasswords[] = $user->getPassword();
            if (count($previousPasswords) > $this->maxSavedPasswords) {
                array_shift($previousPasswords);
            }

            $newPassword = uniqid();
            $this->addUserPassword($user, $newPassword);

            $expectedSavedPasswords = $i;
            if ($i > $this->maxSavedPasswords) {
                $expectedSavedPasswords = $this->maxSavedPasswords;
            }

            self::assertCount($expectedSavedPasswords, $user->getPreviousPasswords());
            self::assertSame($previousPasswords, $user->getPreviousPasswords());
        }
    }

    private function addUserPassword(Administrator $user, string $password): void
    {
        if (null !== $user->getPassword()) {
            $user->addPreviousPassword($user->getPassword());
        }
        $user->setPassword($password);
        $this->userRepository->save($user);
    }

    public function testUserPermissionsIncludeBothOldPermissionsAndNewPermissions(): void
    {
        $managerPortalRole = new ManagerPortalRole();
        $managerPortalRole->setPermissions(['admin_remittances_get', 'admin_transactions_lists']);
        $managerPortalRole->setNewPermissions(['administrators.create']);

        $user = new Administrator();
        $user->addManagerPortalRole($managerPortalRole);

        self::assertSame([
            'admin_remittances_get',
            'admin_transactions_lists',
            'administrators.create',
        ], $user->getPermissions());
    }
}
