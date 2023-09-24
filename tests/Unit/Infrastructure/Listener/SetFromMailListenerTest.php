<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Listener;

use App\Infrastructure\Listener\SetFromMailListener;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SetFromMailListenerTest extends UnitTestCase
{
    public function testSubscriberWillChangeFromEmailAddressOnMessage(): void
    {
        $firstMail = 'first@from.com';
        $secondMail = 'second@from.com';

        $mail = new Email();
        $mail->from($firstMail);

        $messageEvent = new MessageEvent($mail, $this->createStub(Envelope::class), 'transport');

        $subscriber = new SetFromMailListener($secondMail);
        $subscriber->__invoke($messageEvent);

        $addresses = array_map(static fn (Address $address) => $address->getAddress(), $mail->getFrom());
        self::assertNotContains($firstMail, $addresses);
        self::assertContains($secondMail, $addresses);
    }
}
