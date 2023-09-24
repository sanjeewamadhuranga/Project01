<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Interfaces\Activeable;
use App\Infrastructure\Repository\CountryRepository;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraints as Assert;

#[Unique(fields: ['countryCode'])]
#[MongoDB\Document(collection: 'config_countries', repositoryClass: CountryRepository::class)]
#[MongoDB\HasLifecycleCallbacks]
class Country extends BaseDocument implements Activeable
{
    private const FLAG_OFFSET = 0x1F1E5;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $countryName = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 2)]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $countryCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $flag = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $countryISO3Code = null;

    #[Assert\Regex(pattern: '/^\+[1-9][0-9]{0,2}$/'), Assert\NotBlank]
    #[Assert\Length(min: 2, max: 4)]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $dialingCode = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $enabled = true;

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    #[MongoDB\PreUpdate]
    #[MongoDB\PrePersist]
    public function setCountryDetail(): void
    {
        if (null !== $this->countryCode) {
            $this->countryName = Countries::getName($this->countryCode, 'en');
            $this->flag = self::countryCodeToFlag($this->countryCode);
            $this->countryISO3Code = Countries::getAlpha3Code($this->countryCode);
        }
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function getCountryISO3Code(): ?string
    {
        return $this->countryISO3Code;
    }

    public function getDialingCode(): ?string
    {
        return $this->dialingCode;
    }

    public function setDialingCode(?string $dialingCode): void
    {
        $this->dialingCode = $dialingCode;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public static function countryCodeToFlag(string $countryCode): string
    {
        return (string) preg_replace_callback(
            '/./',
            static fn (array $letter) => mb_chr(ord($letter[0]) % 32 + self::FLAG_OFFSET),
            $countryCode
        );
    }

    public function isActive(): bool
    {
        return $this->enabled;
    }

    public function setActive(bool $active): void
    {
        $this->enabled = $active;
    }
}
