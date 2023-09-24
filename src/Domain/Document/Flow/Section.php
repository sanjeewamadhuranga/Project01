<?php

declare(strict_types=1);

namespace App\Domain\Document\Flow;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class Section
{
    use HasTranslation;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $key;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $title;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $description;

    /**
     * @var Collection<int, Screen>
     */
    #[MongoDB\EmbedMany(targetDocument: Screen::class)]
    protected Collection $screens;

    public function __construct()
    {
        $this->screens = new ArrayCollection();
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
     * @return Collection<int, Screen>
     */
    public function getScreens(): Collection
    {
        return $this->screens;
    }

    public function addScreen(Screen $screen): void
    {
        $this->screens->add($screen);
    }

    public function removeScreen(Screen $screen): void
    {
        $this->screens->removeElement($screen);
    }
}
