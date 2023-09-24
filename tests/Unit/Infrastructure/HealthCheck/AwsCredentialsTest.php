<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\HealthCheck;

use App\Infrastructure\HealthCheck\AwsCredentials;
use App\Tests\Unit\UnitTestCase;
use AsyncAws\Core\Credentials\CredentialProvider;
use AsyncAws\Core\Credentials\Credentials;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use PHPUnit\Framework\MockObject\Stub;

class AwsCredentialsTest extends UnitTestCase
{
    private readonly CredentialProvider&Stub $credentialProvider;

    private readonly AwsCredentials $awsCredentials;

    protected function setUp(): void
    {
        parent::setUp();
        $this->credentialProvider = $this->createStub(CredentialProvider::class);
        $this->awsCredentials = new AwsCredentials($this->credentialProvider);
    }

    public function testItWillReturnFailureResultWhenEmptyAwsCredentialIsProvided(): void
    {
        self::assertInstanceOf(Failure::class, $this->awsCredentials->check());
        self::assertSame('Check AWS Configuration', $this->awsCredentials->getLabel());
    }

    public function testItWillReturnFailureResultWhenProvidingEmptyAccessKeyId(): void
    {
        $credentials = new Credentials('', 'something');
        $this->credentialProvider->method('getCredentials')->willReturn($credentials);
        $check = $this->awsCredentials->check();
        self::assertInstanceOf(Failure::class, $check);
        self::assertSame('Missing AWS Access Key Id', $check->getMessage());
    }

    public function testItWillReturnFailureResultWhenProvidingEmptySecretKey(): void
    {
        $credentials = new Credentials('something', '');
        $this->credentialProvider->method('getCredentials')->willReturn($credentials);
        $check = $this->awsCredentials->check();
        self::assertInstanceOf(Failure::class, $check);
        self::assertSame('Missing AWS Secret Key', $check->getMessage());
    }

    public function testItWillReturnSuccessResult(): void
    {
        $credentials = new Credentials('something', 'something');
        $this->credentialProvider->method('getCredentials')->willReturn($credentials);
        $check = $this->awsCredentials->check();
        self::assertInstanceOf(Success::class, $check);
        self::assertSame('All AWS credentials are successfully configured', $check->getMessage());
    }
}
