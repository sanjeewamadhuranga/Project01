<?php

declare(strict_types=1);

namespace App\Domain\Document\Traits;

use App\Domain\Document\Flow\Section;
use App\Infrastructure\Validator\UniqueKey;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

trait FlowTrait
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $name = '';

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $key = '';

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $default = false;

    /**
     * @var Collection<int, Section>
     */
    #[UniqueKey]
    #[MongoDB\EmbedMany(targetDocument: Section::class)]
    protected Collection $sections;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::COLLECTION)]
    protected array $locales = [];

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    /**
     * @param Collection<int, Section> $sections
     */
    public function setSections(Collection $sections): void
    {
        $this->sections = $sections;
    }

    /**
     * @return string[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @param string[] $locales
     */
    public function setLocales(array $locales): void
    {
        $this->locales = $locales;
    }

    public function __clone()
    {
        $this->id = null;
    }
}
