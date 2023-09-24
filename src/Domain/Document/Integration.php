<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Integration\Topic;
use App\Domain\Integration\Type;
use App\Infrastructure\Repository\IntegrationRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: 'integrations', repositoryClass: IntegrationRepository::class)]
class Integration extends BaseDocument implements Stringable
{
    #[Assert\NotBlank, Assert\Length(max: 25)]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $name = null;

    #[MongoDB\Field(enumType: Type::class)]
    protected ?Type $type = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $webhookEncryption = false;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $webhook = null;

    #[Assert\Email]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $email = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $list = null;

    /**
     * @var Topic[]
     */
    #[MongoDB\Field(type: 'integration_topics')]
    protected array $topics = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): void
    {
        $this->type = $type;
    }

    public function isWebhookEncryption(): bool
    {
        return $this->webhookEncryption;
    }

    public function setWebhookEncryption(bool $webhookEncryption): void
    {
        $this->webhookEncryption = $webhookEncryption;
    }

    public function getWebhook(): ?string
    {
        return $this->webhook;
    }

    public function setWebhook(?string $webhook): void
    {
        $this->webhook = $webhook;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getList(): ?string
    {
        return $this->list;
    }

    public function setList(?string $list): void
    {
        $this->list = $list;
    }

    /**
     * @return Topic[]
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    /**
     * @param Topic[] $topics
     */
    public function setTopics(array $topics): void
    {
        $this->topics = $topics;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
