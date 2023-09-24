<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Repository\Security\ManagerPortalRoleRepository;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Infrastructure\Validator\ProtectedRole;
use App\Tests\Feature\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdministratorsControllerTest extends BaseTestCase
{
    private UserRepository $userRepository;

    private ManagerPortalRoleRepository $managerPortalRoleRepository;

    private const SUCCESS_ALERT_SELECTOR = '.alert.alert-success';
    private const INVALID_FEEDBACK_SELECTOR = '.invalid-feedback';

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTouchesDb();

        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->managerPortalRoleRepository = self::getContainer()->get(ManagerPortalRoleRepository::class);
    }

    public function testItExportsUsers(): void
    {
        self::$client->request('GET', '/administrators/export');
        $expectedOutput = <<<OUTPUT
            Username,Email,Enabled?,Assigned_Role,Last_Login,Locale,GoogleId
            ryan@pay.com,ryan@pay.com,Enabled,"ROLE_SUPER_ADMIN | ROLE__ADMIN",2021-10-01T15:00:00+00:00,,
            simon@pay.com,simon@pay.com,Enabled,"ROLE_SUPER_ADMIN | ROLE__ADMIN",2021-10-01T15:00:00+00:00,,
            ion@pay.com,ion@pay.com,Enabled,"ROLE_SUPER_ADMIN | ROLE__ADMIN",2021-10-01T15:00:00+00:00,,
            test@pay.com,test@pay.com,Enabled,"ROLE_SUPER_ADMIN | ROLE__ADMIN | ROLE_WHICH_PROTECTS",2021-10-01T15:00:00+00:00,,
            admin1@pay.com,admin1@pay.com,Enabled,"ROLE_SUPER_ADMIN | ROLE__ADMIN",2021-10-01T15:00:00+00:00,,
            disabled@pay.com,disabled@pay.com,Disabled,"ROLE_SUPER_ADMIN | ROLE__ADMIN",2021-10-01T15:00:00+00:00,,
            roles@pay.com,roles@pay.com,Enabled,ROLE_MANAGER_ROLES_ADMIN,-,,
            user_creator@pay.com,user_creator@pay.com,Enabled,ROLE_USER_CREATOR,-,,
            user_with_protected_role@pay.com,user_with_protected_role@pay.com,Enabled,"ROLE_WHICH_PROTECTS | ROLE_USER_CREATOR",-,,

            OUTPUT;

        self::assertSame(str_replace("\n", "\r\n", $expectedOutput), self::$client->getInternalResponse()->getContent());
    }

    public function testIndexPage(): void
    {
        self::$client->request('GET', '/administrators');
        self::assertResponseIsSuccessful();
        self::$client->request('GET', '/administrators/list');
        self::assertResponseIsSuccessful();
        $this->assertGridResponse();

        $responseValues = $this->getResponseStrings();

        self::assertContains('ryan@pay.com', $responseValues);
        self::assertContains('simon@pay.com', $responseValues);
        self::assertContains('ion@pay.com', $responseValues);
        self::assertContains('test@pay.com', $responseValues);
    }

    public function testCreatingUserIsSuccessful(): void
    {
        $this->createUserWithEmail('abc@dummy.email');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains(self::SUCCESS_ALERT_SELECTOR, 'Successfully created!');
    }

    public function testDeletingUserScramblesTheirEmailAndAllowsToCreateNewUserWithSameEmail(): void
    {
        $user = $this->getUserWith2faEnabled();
        $originalEmail = $user->getEmailCanonical();

        self::assertTrue($user->isEnabled());

        self::$client->request('DELETE', sprintf('/administrators/%s/delete', $user->getId()));
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $user = $this->refresh($user);
        self::assertFalse($user->isEnabled());
        self::assertTrue($user->isDeleted());
        self::assertNotSame($originalEmail, $user->getEmailCanonical());
        self::assertMatchesRegularExpression(
            sprintf('/^deleted_\d+_%s$/', preg_quote($originalEmail, '/')),
            $user->getEmailCanonical()
        );

        $this->createUserWithEmail($originalEmail);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains(self::SUCCESS_ALERT_SELECTOR, 'Successfully created!');
    }

    public function testCreatingUserWithProtectedRoleByUserWithoutThatPermissionWillFail(): void
    {
        $this->authenticate($this->getUserCreator());

        self::$client->request('GET', '/administrators/create');
        self::assertResponseIsSuccessful();

        $protectedRoleName = 'ROLE_PROTECTED_ROLE';
        $result = self::$client->submitForm('Submit', [
            'user' => [
                'email' => 'fail@pay.com',
                'managerPortalRoles' => $this->getManagerPortalRoleArrayForFormSubmission($protectedRoleName),
            ],
        ]);

        self::assertStringContainsString(sprintf((new ProtectedRole())->message, $protectedRoleName), $result->html());
    }

    public function testCreatingUserWithProtectedRoleByUserWithoutThatPermissionWillBeSuccessful(): void
    {
        $this->authenticate($this->getUserWithProtectedRole());

        self::$client->request('GET', '/administrators/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'user' => [
                'email' => 'notFail@pay.com',
                'managerPortalRoles' => $this->getManagerPortalRoleArrayForFormSubmission('ROLE_PROTECTED_ROLE'),
            ],
        ]);
        self::assertResponseIsSuccessful();
    }

    public function testCreatingUserWithUsedEmailWillFail(): void
    {
        self::$client->request('GET', '/administrators/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'user' => [
                'email' => 'abc@dummy.email',
                'managerPortalRoles' => [
                    $this->getManagerPortalRole()->getId(),
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains(self::SUCCESS_ALERT_SELECTOR, 'Successfully created!');

        self::$client->request('GET', '/administrators/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'user' => [
                'email' => 'abc@dummy.email',
                'managerPortalRoles' => [
                    $this->getManagerPortalRole()->getId(),
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains(self::INVALID_FEEDBACK_SELECTOR, 'This value is already used.');
    }

    public function testCreatingInvalidUser(): void
    {
        self::$client->request('GET', '/administrators/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'user' => [
                'email' => 'aaa',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains(self::INVALID_FEEDBACK_SELECTOR, 'This value is not a valid email address.');

        self::$client->request('GET', '/administrators/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'user' => [
                'email' => '',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testCreatingUserWithSuperAdminRole(): void
    {
        $roles = $this->managerPortalRoleRepository->findAll();
        unset($roles[1]);
        $this->testRole($roles);
    }

    public function testCreatingUserWithAdminRole(): void
    {
        $roles = $this->managerPortalRoleRepository->findAll();
        unset($roles[0]);
        $this->testRole($roles);
    }

    public function testCreatingUserWithAllRoles(): void
    {
        $roles = $this->managerPortalRoleRepository->findAll();
        $this->testRole($roles);
    }

    public function testUserEnabling(): void
    {
        $user = $this->getDisableUser();
        self::assertFalse($user->isEnabled());
        self::$client->request('PUT', sprintf('/administrators/%s/enable', $user->getId()));
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertTrue($this->refresh($user)->isEnabled());
    }

    public function testUserDisabling(): void
    {
        $user = $this->getUserWith2faEnabled();
        self::assertTrue($user->isEnabled());
        self::$client->request('PUT', sprintf('/administrators/%s/disable', $user->getId()));
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertFalse($this->refresh($user)->isEnabled());
    }

    /**
     * @return array<int, string>
     */
    private function getResponseStrings(): array
    {
        $responseValues = [];
        $responseArray = $this->getJsonResponse();
        array_walk_recursive($responseArray, function ($item) use (&$responseValues): void {
            $responseValues[] = $item;
        });

        return $responseValues;
    }

    private function createUserWithEmail(string $email): void
    {
        self::$client->request('GET', '/administrators/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'user' => [
                'email' => $email,
                'managerPortalRoles' => [
                    $this->getManagerPortalRole()->getId(),
                ],
            ],
        ]);
    }

    private function getManagerPortalRole(): ManagerPortalRole
    {
        return $this->managerPortalRoleRepository->findAll()[0];
    }

    /**
     * @return array<int, string|null>
     */
    private function getManagerPortalRoleArrayForFormSubmission(string $name): array
    {
        return array_map(
            fn (ManagerPortalRole $role) => $role->getId(),
            array_filter(
                $this->managerPortalRoleRepository->findAll(),
                static fn (ManagerPortalRole $role) => $name === $role->getName()
            )
        );
    }

    /**
     * @param array<int, ManagerPortalRole> $roles
     */
    private function testRole(array $roles): void
    {
        $email = 'roles@dummy.email';
        self::$client->request('GET', '/administrators/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'user' => [
                'email' => $email,
                'managerPortalRoles' => array_map(fn (ManagerPortalRole $role) => $role->getId(), $roles),
            ],
        ]);

        self::assertResponseIsSuccessful();
        /** @var Administrator $user */
        $user = $this->userRepository->findOneBy(['email' => $email]);

        $expectedRoles = array_values(array_map(fn (ManagerPortalRole $role) => $role->getName(), $roles));
        $managerPortalRoles = $user->getManagerPortalRoles()->map(fn (ManagerPortalRole $role) => $role->getName())->toArray();

        self::assertSame($expectedRoles, $managerPortalRoles);
    }

    private function getUserWith2faEnabled(): Administrator
    {
        return static::$fixtures['user_enabled_2fa']; // @phpstan-ignore-line
    }

    private function getDisableUser(): Administrator
    {
        return static::$fixtures['user_disabled']; // @phpstan-ignore-line
    }

    private function getUserCreator(): Administrator
    {
        return static::$fixtures['user_creator']; // @phpstan-ignore-line
    }

    private function getUserWithProtectedRole(): Administrator
    {
        return static::$fixtures['user_with_protected_role']; // @phpstan-ignore-line
    }
}
