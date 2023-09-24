<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Interfaces\Activeable;
use App\Domain\Document\Traits\HasActive;
use App\Infrastructure\Repository\DiscountCodeRepository;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: 'discount_codes', repositoryClass: DiscountCodeRepository::class)]
class DiscountCode extends BaseDocument implements Stringable, Activeable
{
    use HasActive;

    #[Assert\NotBlank]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $code = null;

    #[Assert\NotBlank]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $title = null;

    #[Assert\NotBlank]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $description = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $instructions = null;

    #[Assert\LessThan(propertyPath: 'validTo')]
    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $validFrom = null;

    #[Assert\GreaterThan(propertyPath: 'validFrom')]
    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $validTo = null;

    #[Assert\Range(min: 0, max: 100)]
    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $discountPercentage = 0;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $numberOfTimesUsed = 0;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $daysValid = [];

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $totalDiscountCallback = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): void
    {
        $this->instructions = $instructions;
    }

    public function getValidFrom(): ?DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(?DateTimeInterface $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getValidTo(): ?DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(?DateTimeInterface $validTo): void
    {
        $this->validTo = $validTo;
    }

    public function getDiscountPercentage(): int
    {
        return $this->discountPercentage;
    }

    public function setDiscountPercentage(int $discountPercentage): void
    {
        $this->discountPercentage = $discountPercentage;
    }

    public function getNumberOfTimesUsed(): int
    {
        return $this->numberOfTimesUsed;
    }

    public function setNumberOfTimesUsed(int $numberOfTimesUsed): void
    {
        $this->numberOfTimesUsed = $numberOfTimesUsed;
    }

    /**
     * @return string[]
     */
    public function getDaysValid(): array
    {
        return $this->daysValid;
    }

    /**
     * @param string[] $daysValid
     */
    public function setDaysValid(array $daysValid): void
    {
        $this->daysValid = $daysValid;
    }

    public function getTotalDiscountCallback(): ?string
    {
        return $this->totalDiscountCallback;
    }

    public function setTotalDiscountCallback(?string $totalDiscountCallback): void
    {
        $this->totalDiscountCallback = $totalDiscountCallback;
    }

    public function __toString(): string
    {
        return (string) $this->code;
    }
}
