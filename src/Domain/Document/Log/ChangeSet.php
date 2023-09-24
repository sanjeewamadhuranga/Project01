<?php

declare(strict_types=1);

namespace App\Domain\Document\Log;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use JsonException;
use UnitEnum;

#[MongoDB\EmbeddedDocument]
class ChangeSet
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $field;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::HASH)]
    protected ?array $changes = null;

    /**
     * @param array<int, mixed>|null $changes
     */
    public function __construct(string $field, ?array $changes = null)
    {
        $this->field = $field;

        if (null !== $changes) {
            // canonize the data to make sure it's stored properly
            try {
                $this->changes = (array) json_decode(json_encode($changes, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
            }
            foreach ($changes as $change) {
                if (is_object($change)
                    && !$change instanceof DateTimeInterface
                    && !$change instanceof UnitEnum
                ) {
                    $this->changes = null;
                    break;
                }
            }
        }
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @return string[]
     */
    public function getChanges(): ?array
    {
        return $this->changes;
    }

    /**
     * @param string[] $changes
     */
    public function setChanges(?array $changes): void
    {
        $this->changes = $changes;
    }
}
