<?php

declare(strict_types=1);

namespace App\Domain\Document\Flow;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class Screen
{
    use HasTranslation;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $key;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $title;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $description;

    /**
     * @var Collection<int, Dependency>
     */
    #[MongoDB\EmbedMany(targetDocument: Dependency::class)]
    protected Collection $dependencies;

    public function __construct()
    {
        $this->dependencies = new ArrayCollection();
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection<int, Dependency>
     */
    public function getDependencies(): Collection
    {
        return $this->dependencies;
    }

    /**
     * @param Collection<int, Dependency> $dependencies
     */
    public function setDependencies(Collection $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    public function addDependency(Dependency $dependency): void
    {
        $this->dependencies->add($dependency);
    }

    public function removeDependency(Dependency $dependency): void
    {
        $this->dependencies->removeElement($dependency);
    }
}
