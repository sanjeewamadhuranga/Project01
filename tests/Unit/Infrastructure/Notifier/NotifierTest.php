<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Notifier;

use App\Application\Queue\Bus;
use App\Application\Queue\Commands\SendEmailToIndividualUser;
use App\Application\Queue\Commands\SendSmsNotification;
use App\Domain\Document\Notification\CustomEmail;
use App\Domain\Document\Notification\Notification;
use App\Domain\Document\Notification\PushNotification;
use App\Domain\Document\Notification\Sms;
use App\Infrastructure\Notifier\NotificationAlreadySentException;
use App\Infrastructure\Notifier\Notifier;
use App\Infrastructure\Notifier\UnsupportedNotificationException;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Notifier\Bridge\OneSignal\OneSignalOptions;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\TexterInterface;

class NotifierTest extends UnitTestCase
{
    private Bus&MockObject $bus;

    private TexterInterface&MockObject $texter;

    private Notifier $notifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = $this->createMock(Bus::class);
        $this->texter = $this->createMock(TexterInterface::class);
        $this->notifier = new Notifier($this->texter, $this->createStub(DocumentManager::class), $this->bus);
    }

    public function testItSmsMessageCannotBeNull(): void
    {
        $this->expectExceptionMessage('Message cannot be null');
        $this->notifier->send(new Sms());
    }

    public function testItThrowsAnExceptionIfNotificationHasAlreadyBeenSent(): void
    {
        $notification = $this->createStub(Notification::class);
        $notification->method('isSent')->willReturn(true);
        $this->expectExceptionObject(new NotificationAlreadySentException($notification));
        $this->notifier->send($notification);
    }

//    public function testItSendsPushNotificationAndMarksItAsSent(): void
//    {
//        $pushNotification = new PushNotification();
//        $pushNotification->setMessage('Test message');
//        $pushNotification->setHeadings('Test heading');
//        $pushNotification->setLink('http://www.example.com');
//        $pushNotification->setSub('test-sub');
//
//        $this->texter->expects(self::once())
//            ->method('send')
//            ->with(self::callback(static function (PushMessage $notification) use ($pushNotification) {
//                self::assertSame($pushNotification->getMessage(), $notification->getContent());
//                self::assertSame($pushNotification->getHeadings(), $notification->getSubject());
//                $options = $notification->getOptions();
//                self::assertInstanceOf(OneSignalOptions::class, $options);
//                self::assertSame([$pushNotification->getSub()], $options->toArray()['include_external_user_ids']);
//
//                return true;
//            }));
//
//        $this->notifier->send($pushNotification);
//        self::assertTrue($pushNotification->isSent());
//    }

    public function testItSendEmail(): void
    {
        $email = new CustomEmail();
        $email->setMessage('Test message');
        $email->setTitle('Test Title');

        $this->bus->expects(self::once())->method('dispatch')->with(new SendEmailToIndividualUser($email));
        $this->notifier->send($email);
        self::assertTrue($email->isSent());
    }

    public function testItSendSms(): void
    {
        $sms = new Sms();
        $sms->setMessage('Test message');
        $sms->setPhoneNumber('+6587891823');

        $this->bus->expects(self::once())->method('dispatch')->with(new SendSmsNotification($sms));
        $this->notifier->send($sms);
        self::assertTrue($sms->isSent());
    }

    public function testItThrowsExceptionWhenInvalidNotificationIsPassed(): void
    {
        $notification = $this->createStub(Notification::class);
        $notification->method('getMessage')->willReturn('test');

        $this->expectException(UnsupportedNotificationException::class);
        $this->notifier->send($notification);
    }
}
