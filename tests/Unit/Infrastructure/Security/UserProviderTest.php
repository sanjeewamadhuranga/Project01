<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Infrastructure\Security\UserProvider;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @group security
 */
class UserProviderTest extends UnitTestCase
{
    private UserRepository&MockObject $userRepository;

    private UserProvider $userProvider;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userProvider = new UserProvider($this->userRepository);
    }

    public function testItLoadsUserByUserIdentifier(): void
    {
        $user = $this->createStub(Administrator::class);

        $this->userRepository->expects(self::once())
            ->method('getUserByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        self::assertSame($user, $this->userProvider->loadUserByIdentifier('test@example.com'));
    }

    public function testItThrowsExceptionWhenInvalidUserProvidedToRefreshMethod(): void
    {
        $user = $this->createStub(UserInterface::class);
        $this->expectException(UnsupportedUserException::class);
        $this->userProvider->refreshUser($user);
    }

    public function testItThrowsExceptionWhenUserIsNotFound(): void
    {
        $this->userRepository->expects(self::once())
            ->method('getUserByEmail')
            ->with('test2@example.com')
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->userProvider->loadUserByIdentifier('test2@example.com');
    }
}
