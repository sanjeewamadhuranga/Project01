<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;

final class OneSignalTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): OneSignalTransport
    {
        if ('onesignal' !== $dsn->getScheme()) {
            throw new UnsupportedSchemeException($dsn, 'onesignal', $this->getSupportedSchemes());
        }

        $appId = $this->getUser($dsn);
        $apiKey = $this->getPassword($dsn);
        $defaultRecipientId = $dsn->getOption('defaultRecipientId');
        $host = 'default' === $dsn->getHost() ? null : $dsn->getHost();
        $port = $dsn->getPort();

        return (new OneSignalTransport($appId, $apiKey, $defaultRecipientId, $this->client, $this->dispatcher))->setHost($host)->setPort($port);
    }

    protected function getSupportedSchemes(): array
    {
        return ['onesignal'];
    }
}
