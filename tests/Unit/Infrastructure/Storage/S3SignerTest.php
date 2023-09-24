<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Storage;

use App\Application\Bucket\BucketName;
use App\Infrastructure\Storage\S3Signer;
use App\Tests\Unit\UnitTestCase;
use AsyncAws\S3\S3Client;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;

class S3SignerTest extends UnitTestCase
{
    private S3Client&MockObject $client;

    private S3Signer $signer;

    protected function setUp(): void
    {
        $this->client = $this->createMock(S3Client::class);

        $this->signer = new S3Signer($this->client, [BucketName::TRANSACTION->name => 'transaction_bucket']);

        parent::setUp();
    }

    public function testItThrowsExceptionWhenNoBucketFount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported bucket type passed');

        $this->signer->getSignedDownloadUrl(BucketName::COMPLIANCE, 'key');
    }

    public function testSignedUrlContainsKeyTodayDateAndExpiration(): void
    {
        $key = uniqid('myKey', true);
        $now = new DateTime();
        $expiry = new DateTimeImmutable('+2 days');
        $expirationHours = $expiry->getTimestamp() - $now->getTimestamp();

        $localSigner = new S3Signer(new S3Client(), [BucketName::TRANSACTION->name => 'transaction_bucket']);
        $signedUrl = $localSigner->getSignedDownloadUrl(BucketName::TRANSACTION, $key, $expiry);

        self::assertStringContainsString($key, $signedUrl);
        self::assertStringContainsString('transaction_bucket', $signedUrl);
        self::assertStringContainsString((string) $expirationHours, $signedUrl);
        self::assertStringContainsString($now->format('Ymd').'T'.$now->format('His'), $signedUrl);
    }
}
