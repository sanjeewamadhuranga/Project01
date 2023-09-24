<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Security\Administrator as SecurityUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\EmbeddedDocument]
class AccountManagerNote extends BaseDocument
{
    #[Assert\Length(max: 50)]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $title = null;

    #[Assert\Length(max: 100)]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $note;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $tag = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $followUpAction = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $completed;

    #[Gedmo\Blameable(on: 'create')]
    #[MongoDB\ReferenceOne(targetDocument: SecurityUser::class)]
    protected SecurityUser $user;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): void
    {
        $this->tag = $tag;
    }

    public function getFollowUpAction(): ?string
    {
        return $this->followUpAction;
    }

    public function setFollowUpAction(?string $followUpAction): void
    {
        $this->followUpAction = $followUpAction;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getUser(): SecurityUser
    {
        return $this->user;
    }

    public function setUser(SecurityUser $user): void
    {
        $this->user = $user;
    }
}
