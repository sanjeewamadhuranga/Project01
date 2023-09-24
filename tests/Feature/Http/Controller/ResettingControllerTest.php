<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Domain\Document\Security\Administrator;
use App\Tests\Feature\BaseTestCase;
use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

class ResettingControllerTest extends BaseTestCase
{
    use MailerAssertionsTrait;

    protected static bool $authenticate = false;

    public function testUserMayRequestPasswordResetEmail(): void
    {
        $this->markTouchesDb();
        self::$client->enableProfiler();
        self::$client->followRedirects();
        $user = $this->getTestUser();
        self::$client->request('GET', '/forgot/request');
        self::$client->submitForm('reset', ['email' => $user->getEmail()]);

        self::assertEmailCount(1);

        $email = self::getMailerMessage();
        self::assertInstanceOf(TemplatedEmail::class, $email);
        self::assertSame('Reset Password', $email->getSubject());

        $token = $this->refresh($user)->getConfirmationToken();
        self::assertNotNull($token);

        self::assertEmailHtmlBodyContains($email, 'Click on the link below to reset your password.');
        self::assertEmailHtmlBodyContains($email, $token);

        self::assertStringContainsString($user->getEmail(), self::$client->getCrawler()->html());
        self::$client->request('GET', '/forgot/reset/'.$token);
        $this->getDocumentManager()->clear();

        // Too short password triggers an error
        $crawler = self::$client->submitForm('Set password', ['plainPassword[first]' => 'test', 'plainPassword[second]' => 'test']);
        self::assertStringContainsString('This value is too short', $crawler->html());

        // Password should change if form is valid
        $crawler = self::$client->submitForm('Set password', ['plainPassword[first]' => 'Test1234567890^', 'plainPassword[second]' => 'Test1234567890^']);
        self::assertStringContainsString('Log in', $crawler->html());

        // Password has changed and token is cleared

        $user = $this->refresh($user);
        self::assertNotNull($user->getPassword());
        self::assertTrue(password_verify('Test1234567890^', $user->getPassword()));
        self::assertNull($user->getPasswordRequestedAt());
        self::assertNull($user->getConfirmationToken());
    }

    public function testExpiredTokenDoesNotAllowToChangePassword(): void
    {
        $this->markTouchesDb();
        $user = new Administrator();
        $user->setEmail('test@example.com');
        $user->setConfirmationToken('test1');
        $user->setPasswordRequestedAt(new DateTime('1 hour ago'));
        $this->getDocumentManager()->persist($user);

        self::$client->request('GET', '/forgot/reset/test1');
        self::assertResponseRedirects();
        self::$client->followRedirect();
        self::assertPageTitleContains('Log in');
    }
}
