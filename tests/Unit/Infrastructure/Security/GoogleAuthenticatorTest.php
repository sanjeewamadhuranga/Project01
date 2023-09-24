<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Infrastructure\Security\GoogleAuthenticator;
use App\Tests\Unit\UnitTestCase;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * @group security
 */
class GoogleAuthenticatorTest extends UnitTestCase
{
    private readonly OAuth2ClientInterface&MockObject $oauth2Client;

    private readonly UserProviderInterface&MockObject $userProvider;

    private readonly UserRepository&MockObject $userRepository;

    private readonly GoogleAuthenticator $authenticator;

    public function setUp(): void
    {
        parent::setUp();

        $this->oauth2Client = $this->createMock(OAuth2ClientInterface::class);
        $this->userProvider = $this->createMock(UserProviderInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->authenticator = new GoogleAuthenticator(
            $this->getClientRegistry(),
            $this->userProvider,
            $this->userRepository,
            $this->createStub(AuthenticationSuccessHandlerInterface::class),
            $this->createStub(AuthenticationFailureHandlerInterface::class)
        );
    }

    public function testItSupportsGoogleLoginPath(): void
    {
        self::assertTrue($this->authenticator->supports(new Request(attributes: ['_route' => 'google_login'])));
        self::assertFalse($this->authenticator->supports(new Request(attributes: ['_route' => 'test1'])));
    }

    public function testItAuthenticatesUserAndUpdatesAvatar(): void
    {
        $user = new Administrator();
        $user->setEmail('test@pay.com');

        $this->oauth2Client->expects(self::once())->method('fetchUserFromToken')->willReturn(new GoogleUser([
            'sub' => 'test-sub',
            'picture' => 'avatar-url',
            'email' => 'test@pay.com',
        ]));

        $this->oauth2Client->expects(self::once())->method('getAccessToken')
            ->willReturn(new AccessToken(['access_token' => 'test-access-token']));

        $this->userProvider->expects(self::once())->method('loadUserByIdentifier')->with('test@pay.com')->willReturn($user);
        $this->userRepository->expects(self::once())->method('save')
            ->with(self::callback(static fn (Administrator $user) => 'avatar-url' === $user->getAvatar()));

        $pass = $this->authenticator->authenticate($this->createStub(Request::class));
        self::assertInstanceOf(UserBadge::class, $userBadge = $pass->getBadge(UserBadge::class));
        $userBadge->getUser(); // Trigger user loader
    }

    private function getClientRegistry(): ClientRegistry
    {
        $clientRegistry = $this->createMock(ClientRegistry::class);
        $clientRegistry->method('getClient')->with('google')->willReturn($this->oauth2Client);

        return $clientRegistry;
    }
}
