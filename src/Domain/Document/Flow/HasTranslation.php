<?php

declare(strict_types=1);

namespace App\Domain\Document\Flow;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

trait HasTranslation
{
    /** @var array<string, string> */
    #[MongoDB\Field(type: MongoDBType::HASH)]
    private array $titleTranslations = [];

    /** @var array<string, string> */
    #[MongoDB\Field(type: MongoDBType::HASH)]
    private array $descriptionTranslations = [];

    /**
     * @return array<string, string>
     */
    public function getTitleTranslations(): array
    {
        return $this->titleTranslations;
    }

    /**
     * @param array<string, string> $titleTranslations
     */
    public function setTitleTranslations(array $titleTranslations): void
    {
        $this->titleTranslations = $titleTranslations;
    }

    /**
     * @return array<string, string>
     */
    public function getDescriptionTranslations(): array
    {
        return $this->descriptionTranslations;
    }

    /**
     * @param array<string, string> $descriptionTranslations
     */
    public function setDescriptionTranslations(array $descriptionTranslations): void
    {
        $this->descriptionTranslations = $descriptionTranslations;
    }
}
