<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration\ManagerRole;

use App\Infrastructure\Form\Configuration\ManagerRole\ManagerRoleType;
use App\Infrastructure\Form\Configuration\ManagerRole\PermissionsType;
use App\Tests\Unit\Infrastructure\Form\TypeTestCaseWithManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class ManagerRoleTypeTest extends TypeTestCaseWithManagerRegistry
{
    private Security&Stub $security;

    protected function setUp(): void
    {
        $this->security = $this->createStub(Security::class);

        parent::setUp();
    }

    public function testItThrowsExceptionWhenNoPermissionsForGivenRole(): void
    {
        $this->security->method('isGranted')->willReturn(false);

        $this->expectException(AccessDeniedException::class);

        $this->factory->create(ManagerRoleType::class);
    }

    public function testItDisplaysProtectedByRoleFieldWhenUserHasAdminRole(): void
    {
        $this->security->method('isGranted')->willReturn(true);

        $form = $this->factory->create(ManagerRoleType::class);

        self::assertInstanceOf(Form::class, $form->get('protectedByRole'));
    }

    public function testItDoesNotDisplayProtectedByRoleFieldWhenUserHasNotAdminRole(): void
    {
        $this->security->method('isGranted')->willReturnOnConsecutiveCalls(true, false);
        $form = $this->factory->create(ManagerRoleType::class);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Child "protectedByRole" does not exist.');

        $form->get('protectedByRole');
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        $permissionSecurity = $this->createStub(Security::class);
        $permissionSecurity->method('isGranted')->willReturn(true);

        return [
            new PreloadedExtension([new ManagerRoleType($this->security)], []),
            new PreloadedExtension([new PermissionsType($permissionSecurity)], []),
            new PreloadedExtension([new DocumentType($this->getManagerRegistry())], []),
        ];
    }
}
