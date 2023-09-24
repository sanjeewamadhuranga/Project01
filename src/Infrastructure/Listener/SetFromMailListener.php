<?php

declare(strict_types=1);

namespace App\Infrastructure\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsEventListener]
class SetFromMailListener
{
    public function __construct(private readonly string $fromEmail, private readonly string $senderName = '')
    {
    }

    public function __invoke(MessageEvent $event): void
    {
        $email = $event->getMessage();

        if (!$email instanceof Email) {
            return;
        }

        $email->from(new Address($this->fromEmail, $this->senderName));
    }
}
