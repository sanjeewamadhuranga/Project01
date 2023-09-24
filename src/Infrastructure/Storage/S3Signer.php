<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Application\Bucket\BucketName;
use AsyncAws\S3\Input\GetObjectRequest;
use AsyncAws\S3\S3Client;
use DateTimeImmutable;
use InvalidArgumentException;

class S3Signer
{
    /**
     * @param array<string, string> $buckets
     */
    public function __construct(private readonly S3Client $s3Client, private readonly array $buckets)
    {
    }

    public function getSignedDownloadUrl(BucketName $bucketName, string $key, ?DateTimeImmutable $expiry = null): string
    {
        $bucket = $this->buckets[$bucketName->name] ?? null;

        if (null === $bucket) {
            throw new InvalidArgumentException('Unsupported bucket type passed');
        }

        return $this->s3Client->presign(new GetObjectRequest([
            'Bucket' => $bucket,
            'Key' => $key,
        ]), $expiry ?? new DateTimeImmutable('+30 minutes'));
    }
}
