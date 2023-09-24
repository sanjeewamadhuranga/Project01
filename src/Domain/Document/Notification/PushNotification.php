<?php

declare(strict_types=1);

namespace App\Domain\Document\Notification;

use App\Infrastructure\Repository\Notification\PushNotificationRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'comms_pushnotifications', repositoryClass: PushNotificationRepository::class)]
class PushNotification extends AbstractNotification
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $link = null;

    #[MongoDB\Field(name: 'contents', type: MongoDBType::STRING)]
    protected ?string $message = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $headings;

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function getHeadings(): string
    {
        return $this->headings;
    }

    public function setHeadings(string $headings): void
    {
        $this->headings = $headings;
    }

    public function getTitle(): string
    {
        return $this->getHeadings();
    }

    public function getRecipient(): ?string
    {
        return null;
    }
}
