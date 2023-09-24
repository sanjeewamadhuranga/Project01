<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use Stringable;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Notifier\Bridge\OneSignal\OneSignalOptions;
use Symfony\Component\Notifier\Exception\LogicException;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[When('never')]
final class OneSignalTransport extends AbstractTransport implements Stringable
{
    protected const HOST = 'onesignal.com';

    public function __construct(
        private readonly string $appId,
        private readonly string $apiKey,
        private readonly ?string $defaultRecipientId = null,
        HttpClientInterface $client = null,
        EventDispatcherInterface $dispatcher = null
    ) {
        parent::__construct($client, $dispatcher);
    }

    public function __toString(): string
    {
        if (null === $this->defaultRecipientId) {
            return sprintf('onesignal://%s@%s', urlencode($this->appId), $this->getEndpoint());
        }

        return sprintf('onesignal://%s@%s?recipientId=%s', urlencode($this->appId), $this->getEndpoint(), $this->defaultRecipientId);
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof PushMessage && (null !== $this->defaultRecipientId || ($message->getOptions() instanceof OneSignalOptions && null !== $message->getOptions()->getRecipientId()));
    }

    /**
     * @see https://documentation.onesignal.com/reference/create-notification
     */
    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof PushMessage) {
            throw new UnsupportedMessageTypeException(self::class, PushMessage::class, $message);
        }

        if (null !== $message->getOptions() && !$message->getOptions() instanceof OneSignalOptions) {
            throw new LogicException(sprintf('The "%s" transport only supports instances of "%s" for options.', self::class, OneSignalOptions::class));
        }

        if ((($opts = $message->getOptions()) !== null) && ($notification = $message->getNotification()) !== null) {
            $opts = OneSignalOptions::fromNotification($notification);
        }

        $recipientId = $message->getRecipientId() ?? $this->defaultRecipientId;

        if (null === $recipientId) {
            throw new LogicException(sprintf('The "%s" transport should have configured `defaultRecipientId` via DSN or provided with message options.', self::class));
        }

        $options = null !== $opts ? $opts->toArray() : [];
        $options['app_id'] = $this->appId;
        $options['include_external_user_ids'] = [$recipientId];

        if (!isset($options['headings'])) {
            $options['headings'] = ['en' => $message->getSubject()];
        }
        if (!isset($options['contents'])) {
            $options['contents'] = ['en' => $message->getContent()];
        }

        $response = $this->client->request('POST', 'https://'.$this->getEndpoint().'/api/v1/notifications', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic '.$this->apiKey,
            ],
            'json' => $options,
        ]);

        try {
            $statusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            throw new TransportException('Could not reach the remote OneSignal server.', $response, 0, $e);
        }

        if (200 !== $statusCode) {
            throw new TransportException(sprintf('Unable to send the OneSignal push notification: "%s".', $response->getContent(false)), $response);
        }

        $result = $response->toArray(false);

        if (!$result['id']) {
            throw new TransportException(sprintf('Unable to send the OneSignal push notification: "%s".', $response->getContent(false)), $response);
        }

        $sentMessage = new SentMessage($message, (string) $this);
        $sentMessage->setMessageId($result['id']);

        return $sentMessage;
    }
}
