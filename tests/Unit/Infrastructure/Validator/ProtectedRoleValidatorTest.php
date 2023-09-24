<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Validator;

use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Validator\ProtectedRole;
use App\Infrastructure\Validator\ProtectedRoleValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Hydrator\HydratorFactory;
use Doctrine\ODM\MongoDB\PersistentCollection;
use Doctrine\ODM\MongoDB\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<ProtectedRoleValidator>
 */
class ProtectedRoleValidatorTest extends ConstraintValidatorTestCase
{
    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private ProtectedRole $protectedRole;

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->protectedRole = new ProtectedRole();

        parent::setUp();
    }

    public function testNewAssignedRolesWithoutProtectedByRoles(): void
    {
        $role_1 = $this->createStub(ManagerPortalRole::class);
        $role_2 = $this->createStub(ManagerPortalRole::class);
        $role_3 = $this->createStub(ManagerPortalRole::class);
        $role_4 = $this->createStub(ManagerPortalRole::class);

        $this->authorizationChecker->method('isGranted')
            ->withConsecutive(['PROTECT_ROLE_BY_OTHER_ROLE', $role_1], ['PROTECT_ROLE_BY_OTHER_ROLE', $role_2], ['PROTECT_ROLE_BY_OTHER_ROLE', $role_3], ['PROTECT_ROLE_BY_OTHER_ROLE', $role_4])
            ->willReturn(true, true, true, true);

        $this->validator->validate(new ArrayCollection([$role_1, $role_2, $role_3, $role_4]), $this->protectedRole);

        $this->assertNoViolation();
    }

    public function testEditedAssignedRolesWithoutProtectedByRoles(): void
    {
        $role_1 = $this->createStub(ManagerPortalRole::class);
        $role_2 = $this->createStub(ManagerPortalRole::class);
        $role_3 = $this->createStub(ManagerPortalRole::class);
        $role_4 = $this->createStub(ManagerPortalRole::class);

        $this->authorizationChecker->method('isGranted')
            ->withConsecutive(['PROTECT_ROLE_BY_OTHER_ROLE', $role_1], ['PROTECT_ROLE_BY_OTHER_ROLE', $role_2], ['PROTECT_ROLE_BY_OTHER_ROLE', $role_3], ['PROTECT_ROLE_BY_OTHER_ROLE', $role_4])
            ->willReturn(true, true, true, true);

        $collection = $this->getPersistentCollection([$role_1, $role_2]);
        $collection->takeSnapshot();
        $collection->removeElement($role_1);
        $collection->removeElement($role_2);
        $collection->add($role_3);
        $collection->add($role_4);

        $this->validator->validate($collection, $this->protectedRole);

        $this->assertNoViolation();
    }

    public function testNewAssignedRolesWithProtectedRole(): void
    {
        $roleName = uniqid('name', true);
        $role_1 = $this->createStub(ManagerPortalRole::class);
        $role_2 = $this->getRoleWithName($roleName);

        $this->authorizationChecker->method('isGranted')
            ->withConsecutive(['PROTECT_ROLE_BY_OTHER_ROLE', $role_1], ['PROTECT_ROLE_BY_OTHER_ROLE', $role_2])
            ->willReturn(true, false);

        $this->validator->validate(new ArrayCollection([$role_1, $role_2]), $this->protectedRole);

        $this->buildViolation(sprintf($this->protectedRole->message, $roleName))->assertRaised();
    }

    public function testEditedRolesWithDeletedRoleWhichIsProtected(): void
    {
        $roleName = uniqid('name', true);
        $role_1 = $this->createStub(ManagerPortalRole::class);
        $role_2 = $this->getRoleWithName($roleName);

        $this->authorizationChecker->method('isGranted')
            ->withConsecutive(['PROTECT_ROLE_BY_OTHER_ROLE', $role_2])
            ->willReturn(false);

        $collection = $this->getPersistentCollection([$role_1, $role_2]);
        $collection->takeSnapshot();
        $collection->removeElement($role_2);

        $this->validator->validate($collection, $this->protectedRole);

        $this->buildViolation(sprintf($this->protectedRole->message, $roleName))->assertRaised();
    }

    public function testEditedRolesWithAddedRoleWhichIsProtected(): void
    {
        $roleName = uniqid('name', true);
        $role_1 = $this->createStub(ManagerPortalRole::class);
        $role_2 = $this->getRoleWithName($roleName);

        $this->authorizationChecker->method('isGranted')
            ->withConsecutive(['PROTECT_ROLE_BY_OTHER_ROLE', $role_2])
            ->willReturn(false);

        $collection = $this->getPersistentCollection([$role_1]);
        $collection->takeSnapshot();
        $collection->add($role_2);

        $this->validator->validate($collection, $this->protectedRole);

        $this->buildViolation(sprintf($this->protectedRole->message, $roleName))->assertRaised();
    }

    public function testEditedRolesWithNotTouchedRoleWhichIsProtected(): void
    {
        $role_1 = $this->getRoleWithName('test');
        $role_2 = $this->createStub(ManagerPortalRole::class);
        $role_3 = $this->createStub(ManagerPortalRole::class);

        $this->authorizationChecker->method('isGranted')
            ->withConsecutive(['PROTECT_ROLE_BY_OTHER_ROLE', $role_2], ['PROTECT_ROLE_BY_OTHER_ROLE', $role_2])
            ->willReturn(true, true);

        $collection = $this->getPersistentCollection([$role_1, $role_2]);
        $collection->takeSnapshot();
        $collection->removeElement($role_2);
        $collection->add($role_3);

        $this->validator->validate($collection, $this->protectedRole);

        $this->assertNoViolation();
    }

    protected function createValidator(): ProtectedRoleValidator
    {
        return new ProtectedRoleValidator($this->authorizationChecker);
    }

    private function getRoleWithName(string $name): ManagerPortalRole
    {
        $role = $this->createStub(ManagerPortalRole::class);
        $role->method('getName')->willReturn($name);

        return $role;
    }

    /**
     * @param ManagerPortalRole[] $elements
     *
     * @return PersistentCollection<int, ManagerPortalRole>
     */
    private function getPersistentCollection(array $elements): PersistentCollection
    {
        $dm = $this->createStub(DocumentManager::class);
        $eventManager = $this->createStub(EventManager::class);

        return new PersistentCollection(
            new ArrayCollection($elements),
            $dm,
            new UnitOfWork(
                $dm,
                $eventManager,
                new HydratorFactory(
                    $dm,
                    $eventManager,
                    '/tmp',
                    '/tmp',
                    0
                )
            )
        );
    }
}
