<?php

declare(strict_types=1);

namespace App\Tests\Feature\Traits;

trait JsonResponseTrait
{
    public function assertJsonResponseEquals(mixed $data): void
    {
        self::assertIsJsonResponse();
        self::assertEquals($data, $this->getJsonResponse());
    }

    public function assertGridResponse(): void
    {
        self::assertIsJsonResponse();
        self::assertResponseIsSuccessful();
        $responseArray = $this->getJsonResponse();
        self::assertArrayHasKey('pagination', $responseArray);
        self::assertArrayHasKey('draw', $responseArray);
        self::assertArrayHasKey('data', $responseArray);
        self::assertIsArray($responseArray['data']);
    }

    public static function assertIsJsonResponse(): void
    {
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function getJsonResponse(): mixed
    {
        return json_decode((string) self::$client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}
