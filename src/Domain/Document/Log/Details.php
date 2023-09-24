<?php

declare(strict_types=1);

namespace App\Domain\Document\Log;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\HttpFoundation\Request;

#[MongoDB\EmbeddedDocument]
class Details
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $username;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $browser = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $ip = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $headers;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $userAgent;

    public static function fromRequest(Request $request, string $username): self
    {
        $details = new self();
        $details->setUsername($username);
        $details->setIp($request->getClientIp());
        $details->setUserAgent((string) $request->headers->get('user-agent'));

        return $details;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(?string $browser): void
    {
        $this->browser = $browser;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
    }

    public function getHeaders(): string
    {
        return $this->headers;
    }

    public function setHeaders(string $headers): void
    {
        $this->headers = $headers;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }
}
