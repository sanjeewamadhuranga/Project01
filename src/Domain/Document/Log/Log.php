<?php

declare(strict_types=1);

namespace App\Domain\Document\Log;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\LogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Gedmo\Mapping\Annotation as Gedmo;

#[MongoDB\Document(collection: 'manager_logs', repositoryClass: LogRepository::class, readOnly: true)]
class Log extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $type = null;

    #[MongoDB\Field]
    protected ?string $objectClass = null;

    #[MongoDB\Field]
    protected ?string $objectId = null;

    #[MongoDB\EmbedOne(targetDocument: Details::class)]
    protected ?Details $details = null;

    /**
     * @var Collection<int, ChangeSet>
     */
    #[MongoDB\EmbedMany(targetDocument: ChangeSet::class)]
    protected Collection $changeSets;

    #[Gedmo\Blameable(on: 'create')]
    #[MongoDB\ReferenceOne(targetDocument: Administrator::class)]
    protected ?Administrator $user = null;

    /**
     * Not stored. {@see $objectClass} and {@see $objectId}.
     */
    protected ?object $object = null;

    public function __construct(string $type, ?object $object = null)
    {
        $this->type = $type;
        $this->changeSets = new ArrayCollection();

        $this->setObject($object);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getObject(): ?object
    {
        return $this->object;
    }

    public function setObject(?object $object): void
    {
        $this->object = $object;
        $this->objectClass = null;
        $this->objectId = null;

        if (null !== $object) {
            $this->objectClass = $object::class;
            $this->objectId = method_exists($object, 'getId') ? $object->getId() : null;
        }
    }

    public function getObjectClass(): ?string
    {
        return $this->objectClass;
    }

    public function setObjectClass(?string $objectClass): void
    {
        $this->objectClass = $objectClass;
    }

    public function getObjectId(): ?string
    {
        return $this->objectId;
    }

    public function setObjectId(?string $objectId): void
    {
        $this->objectId = $objectId;
    }

    public function getDetails(): ?Details
    {
        return $this->details;
    }

    public function setDetails(?Details $details): void
    {
        $this->details = $details;
    }

    /**
     * @return Collection<int, ChangeSet>
     */
    public function getChangeSets(): Collection
    {
        return $this->changeSets;
    }

    /**
     * @param Collection<int, ChangeSet> $changeSets
     */
    public function setChangeSets(Collection $changeSets): void
    {
        $this->changeSets = $changeSets;
    }

    public function getUser(): ?Administrator
    {
        return $this->user;
    }

    public function setUser(Administrator $user): void
    {
        $this->user = $user;
    }
}
