<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\EmbeddedDocument]
class NationalIdentity implements Stringable
{
    final public const VERIFIED = 'verified';
    final public const BLACKLISTED = 'blacklisted';
    final public const UNVERIFIED = 'unverified';

    #[Assert\Regex(pattern: '/^([0-9]{9}[x|X|v|V]|^[1-2]{1}[0-9]{11})$/')]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $number = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $status = null;

    public function __construct(?string $number)
    {
        $this->number = $number;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function isInvalid(): bool
    {
        return in_array($this->status, [self::BLACKLISTED, self::UNVERIFIED], true);
    }

    public function __toString(): string
    {
        return (string) $this->number;
    }
}
