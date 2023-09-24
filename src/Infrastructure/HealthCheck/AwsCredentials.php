<?php

declare(strict_types=1);

namespace App\Infrastructure\HealthCheck;

use AsyncAws\Core\Configuration;
use AsyncAws\Core\Credentials\CredentialProvider;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

class AwsCredentials implements CheckInterface
{
    public function __construct(private readonly CredentialProvider $credentialProvider)
    {
    }

    public function check(): ResultInterface
    {
        $credentials = $this->credentialProvider->getCredentials(Configuration::create([]));

        if (null === $credentials) {
            return new Failure('Missing AWS credentials');
        }

        if ('' === $credentials->getAccessKeyId()) {
            return new Failure('Missing AWS Access Key Id');
        }

        if ('' === $credentials->getSecretKey()) {
            return new Failure('Missing AWS Secret Key');
        }

        return new Success('All AWS credentials are successfully configured');
    }

    public function getLabel(): string
    {
        return 'Check AWS Configuration';
    }
}
