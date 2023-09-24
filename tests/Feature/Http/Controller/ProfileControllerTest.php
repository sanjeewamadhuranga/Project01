<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Domain\Document\Security\Administrator;
use App\Domain\Settings\SettingsInterface;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Tests\Feature\BaseTestCase;
use OTPHP\TOTP;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileControllerTest extends BaseTestCase
{
    private UserRepository $userRepository;

    private SystemSettings $systemSettings;

    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->markTouchesDb();

        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->systemSettings = self::getContainer()->get(SystemSettings::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testEnablingF2aForUserWithGoogleSecret(): void
    {
        $this->authenticate($this->getUserWith2faEnabled());
        self::$client->request('GET', '/profile/2fa/enable-app');
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.alert.alert-danger', '2FA is already enabled');
    }

    public function testEnablingF2aWhenItIsDisable(): void
    {
        $this->systemSettings->set(SettingsInterface::DISABLE_MANAGER_PORTAL_PASSWORD_LOGIN, 'true');
        self::$client->request('GET', '/profile/2fa/enable-app');
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.alert.alert-danger', '2FA is not enabled');
    }

    public function testEnablingF2aWithValidCode(): void
    {
        $crawler = self::$client->request('GET', '/profile/2fa/enable-app');
        self::assertResponseIsSuccessful();

        $secret = $crawler->filter('code.user-select-all')->text();

        self::$client->submitForm('Verify', [
            'enable_two_factor_auth' => [
                'code' => TOTP::create($secret)->now(),
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.text-900.mb-3', 'Success!');
        self::assertTrue($this->refresh($this->getTestUser())->isGoogleAuthenticatorEnabled());
    }

    public function testEnablingF2aWithInvalidCode(): void
    {
        self::$client->request('GET', '/profile/2fa/enable-app');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Verify', [
            'enable_two_factor_auth' => [
                'code' => 'abc123',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('.invalid-feedback', 'Invalid authenticator code');
    }

    public function testDisablingF2a(): void
    {
        $user = $this->refresh($this->getUserWith2faEnabled());
        $this->authenticate($user);
        self::assertTrue($user->isGoogleAuthenticatorEnabled());
        self::assertTrue($user->isSmsAuthenticationEnabled());

        self::$client->request('GET', '/profile/2fa/disable');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'password_confirmation' => [
                'password' => 'test',
            ],
        ]);
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        $this->getDocumentManager()->clear();
        $user = $this->refresh($user);
        self::assertFalse($user->isGoogleAuthenticatorEnabled());
        self::assertFalse($user->isSmsAuthenticationEnabled());
    }

    public function testPasswordChangingWhenItIsDisabled(): void
    {
        $this->systemSettings->set(SettingsInterface::DISABLE_MANAGER_PORTAL_PASSWORD_LOGIN, 'true');
        self::$client->request('GET', '/profile/change-password');
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.alert.alert-danger', 'Password login is disabled, you cannot change this setting');
    }

    public function testPasswordChanging(): void
    {
        $currentPassword = 'currentPassword123^%!';
        $newPassword = 'Alcatraz2067*2';

        $user = $this->refresh($this->getTestUser());
        $user->setPassword($this->passwordHasher->hashPassword($user, $currentPassword));
        $this->userRepository->save($user);
        $this->authenticate($user);

        self::$client->request('GET', '/profile/change-password');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'change_password_form' => [
                'currentPassword' => $currentPassword,
                'newPassword' => [
                    'first' => $newPassword,
                    'second' => $newPassword,
                ],
            ],
        ]);
        self::$client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.alert.alert-success', 'Password successfully changed');

        $user = $this->refresh($user);
        self::assertFalse($this->passwordHasher->isPasswordValid($user, $currentPassword));
        self::assertTrue($this->passwordHasher->isPasswordValid($user, $newPassword));
    }

    public function testPasswordChangingWithWrongCurrentPassword(): void
    {
        self::$client->request('GET', '/profile/change-password');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'change_password_form' => [
                'currentPassword' => 'abc123',
                'newPassword' => [
                    'first' => 'abc123',
                    'second' => 'abc123',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('.invalid-feedback', 'The password is invalid.');
    }

    public function testPasswordChangingWithWrongRetypedNewPassword(): void
    {
        $currentPassword = 'currentPassword123^%!';
        $user = $this->refresh($this->getTestUser());
        $user->setPassword($this->passwordHasher->hashPassword($user, $currentPassword));
        $this->userRepository->save($user);
        $this->authenticate($user);

        self::$client->request('GET', '/profile/change-password');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'change_password_form' => [
                'currentPassword' => $currentPassword,
                'newPassword' => [
                    'first' => 'abc123/!@#',
                    'second' => 'abc123',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('.invalid-feedback', 'The password fields must match.');
    }

    private function getUserWith2faEnabled(): Administrator
    {
        return static::$fixtures['user_enabled_2fa']; // @phpstan-ignore-line
    }
}
