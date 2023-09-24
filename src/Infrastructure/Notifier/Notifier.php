<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Queue\Bus;
use App\Application\Queue\Commands\SendEmailToIndividualUser;
use App\Application\Queue\Commands\SendSmsNotification;
use App\Domain\Document\Notification\CustomEmail;
use App\Domain\Document\Notification\Notification;
use App\Domain\Document\Notification\PushNotification;
use App\Domain\Document\Notification\Sms;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Notifier\Bridge\OneSignal\OneSignalOptions;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\TexterInterface;
use UnexpectedValueException;

/**
 * It acts as a facade for notification by accepting the classes that implement notification interface.
 * The usual flow for notifier is.
 *
 * 1. Controller call for forUser method that is implemented by notification interface. {@see Notification::forUser()}
 * 2. Notifier class act as a facade to accept the return notification object and perform the action base on the notification type.
 * 3. If there is another notification type want to add, it can simply add in {@see Notifier::doSendQueue()}.
 */
class Notifier
{
    public function __construct(private readonly TexterInterface $texter, private readonly DocumentManager $dm, private readonly Bus $bus)
    {
    }

    /**
     * @throws NotificationAlreadySentException
     * @throws UnsupportedNotificationException
     */
    public function send(Notification $notification): void
    {
        if ($notification->isSent()) {
            throw new NotificationAlreadySentException($notification);
        }
        $this->dm->persist($notification);
        $this->dm->flush();

        /*
         * Since push notification is directly calling one signal, need to set one signal's return value to meta field.
         * Ticket: https://app.clickup.com/t/2kr8y02
         */
        if ($notification instanceof PushNotification) {
            try {
                $sentMessage = $this->doSendPushNotification($notification);
                $notification->setMeta([
                    'transport' => $sentMessage->getTransport(),
                    'messageId' => $sentMessage->getMessageId(),
                    'original' => [
                        'recipientId' => $sentMessage->getOriginalMessage()->getRecipientId(),
                        'subject' => $sentMessage->getOriginalMessage()->getSubject(),
                        'options' => $sentMessage->getOriginalMessage()->getOptions()?->toArray(),
                        'transport' => $sentMessage->getOriginalMessage()->getTransport(),
                    ],
                ]);
                $notification->setSent(true);
            } catch (TransportException $e) {
                $notification->setMeta($e->getResponse()->toArray());
            }
        } else {
            $this->doSendQueue($notification);
            $notification->setSent(true);
        }
        $this->dm->flush();
    }

    private function doSendPushNotification(PushNotification $pushNotification): SentMessage
    {
        $sentMessage = $this->texter->send($this->originalMessage($pushNotification)->transport('onesignal'));

        if (null === $sentMessage) {
            throw new SentMessageReturnNull($pushNotification);
        }

        return $sentMessage;
    }

    private function originalMessage(PushNotification $pushNotification): PushMessage
    {
        if (null === $pushNotification->getMessage()) {
            throw new UnexpectedValueException('Message cannot be null');
        }

        return new PushMessage(
            $pushNotification->getHeadings(),
            $pushNotification->getMessage(),
            new OneSignalOptions(
                [
                    'contents' => [
                        'en' => $pushNotification->getMessage(),
                    ],
                    'headings' => [
                        'en' => $pushNotification->getHeadings(),
                    ],
                    'data' => [
                        'app_url' => $pushNotification->getLink(),
                    ],
                    'recipient_id' => $pushNotification->getSub(),
                    'include_external_user_ids' => [$pushNotification->getSub()],
                ]
            )
        );
    }

    private function doSendQueue(Notification $notification): void
    {
        if (null === $notification->getMessage()) {
            throw new UnexpectedValueException('Message cannot be null');
        }

        match (true) {
            // $notification instanceof Sms => $this->texter->send(new SmsMessage($notification->getPhoneNumber(), $notification->getMessage())),
            $notification instanceof Sms => $this->bus->dispatch(new SendSmsNotification($notification)),
            $notification instanceof CustomEmail => $this->bus->dispatch(new SendEmailToIndividualUser($notification)),
            default => throw new UnsupportedNotificationException($notification),
        };
    }
}
