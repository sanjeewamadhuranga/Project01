<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Security\UserChecker;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;

/**
 * @group security
 */
class UserCheckerTest extends UnitTestCase
{
    private Administrator&MockObject $user;

    private UserChecker $userChecker;

    protected function setUp(): void
    {
        $this->user = $this->createMock(Administrator::class);

        $this->userChecker = new UserChecker();

        parent::setUp();
    }

    public function testItNoopsWhenInvalidUserSupplied(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->expects(self::never())->method('getUserIdentifier');
        (new UserChecker())->checkPreAuth($user);
    }

    /**
     * @param class-string<Throwable> $exception
     *
     * @dataProvider userWhoThrowsExceptionProvider
     */
    public function testItFailsIfUserIsNotEnabled(string $method, bool $methodResult, string $exception, string $exceptionMessage): void
    {
        $this->user->expects(self::once())->method($method)->willReturn($methodResult);
        $this->expectException($exception);
        $this->expectExceptionMessage($exceptionMessage);

        $this->userChecker->checkPreAuth($this->user);
    }

    public function testItPassesIfUserIsEnabled(): void
    {
        $this->user->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->userChecker->checkPreAuth($this->user);
    }

    /**
     * @return iterable<array{string, bool, class-string<Throwable>, string}>
     */
    public function userWhoThrowsExceptionProvider(): iterable
    {
        yield 'Disabled user' => ['isEnabled', false, DisabledException::class, 'User account is disabled.'];
        yield 'Expired user' => ['isExpired', true, CustomUserMessageAuthenticationException::class, 'User account has expired.'];
        yield 'Suspended user' => ['isSuspended', true, CustomUserMessageAuthenticationException::class, 'User account is suspended!'];
    }
}
