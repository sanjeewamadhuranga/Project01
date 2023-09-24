<?php

declare(strict_types=1);

namespace App\Domain\Document\Notification\CustomEmail;

use App\Domain\Document\BaseDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class History extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $comment;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $eventType;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $state;

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): void
    {
        $this->eventType = $eventType;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }
}
