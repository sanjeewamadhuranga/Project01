<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Setup;

use App\Application\Setup\AdministratorAccountSetupWizardDetector;
use App\Infrastructure\Repository\Security\ManagerPortalRoleRepository;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\Stub;

class AdministratorAccountSetupWizardDetectorTest extends UnitTestCase
{
    private ManagerPortalRoleRepository&Stub $roleRepository;

    private UserRepository&Stub $userRepository;

    protected function setUp(): void
    {
        $this->roleRepository = $this->createStub(ManagerPortalRoleRepository::class);

        $this->userRepository = $this->createStub(UserRepository::class);
    }

    public function testItDoNotNeedSetupWhenEnabledSetupWizard(): void
    {
        self::assertFalse((new AdministratorAccountSetupWizardDetector(false, $this->roleRepository, $this->userRepository))->needSetup());
    }

    public function testItDoNeedSetupWhenNoRolesAndEnabledSetupWizard(): void
    {
        $this->roleRepository->method('countAll')->willReturn(10);
        $this->userRepository->method('countAll')->willReturn(0);

        self::assertTrue((new AdministratorAccountSetupWizardDetector(true, $this->roleRepository, $this->userRepository))->needSetup());
    }

    public function testItDoNeedSetupWhenNoUsersAndEnabledSetupWizard(): void
    {
        $this->roleRepository->method('countAll')->willReturn(0);
        $this->userRepository->method('countAll')->willReturn(10);

        self::assertTrue((new AdministratorAccountSetupWizardDetector(true, $this->roleRepository, $this->userRepository))->needSetup());
    }
}
