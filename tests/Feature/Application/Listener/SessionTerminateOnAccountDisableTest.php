<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Listener;

use App\Domain\Document\Security\Administrator;
use App\Tests\Feature\BaseTestCase;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @group security
 */
class SessionTerminateOnAccountDisableTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTouchesDb();
    }

    /**
     * @dataProvider userDisableCallbackProvider
     */
    public function testUserLogsOutUponAccountDisable(callable $disableUserCallback): void
    {
        self::$client->request('GET', 'profile');
        self::assertSelectorTextContains('h5', 'Profile');
        $tokenStorage = self::$client->getContainer()->get(TokenStorageInterface::class);

        self::assertNotNull($tokenStorage->getToken());

        $user = $this->refresh($this->getTestUser());
        $disableUserCallback($user); // Disable the user
        $this->getDocumentManager()->persist($user);
        $this->getDocumentManager()->flush();

        // Navigate to profile page and expect to be logged out
        self::$client->request('GET', 'profile');
        self::$client->followRedirect();
        self::assertRouteSame('login');
        self::assertNull($tokenStorage->getToken());
    }

    /**
     * @return iterable<array{callable(Administrator): void}>
     */
    public function userDisableCallbackProvider(): iterable
    {
        yield 'enabled = false' => [fn (Administrator $user) => $user->setEnabled(false)];
        yield 'expired' => [fn (Administrator $user) => $user->setAccountExpirationDate(new DateTimeImmutable('yesterday'))];
        yield 'suspended' => [fn (Administrator $user) => $user->suspendAccount()];
    }
}
