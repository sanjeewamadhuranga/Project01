<?php

declare(strict_types=1);

namespace App\Domain\Document\Notification;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Domain\Document\Notification\CustomEmail\History;
use App\Infrastructure\Repository\Notification\CustomEmailRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use UnexpectedValueException;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[MongoDB\Document(collection: 'comms_emails', repositoryClass: CustomEmailRepository::class)]
class CustomEmail extends AbstractNotification
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $title = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $emailAddress;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $mailjetId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $attachmentKey = null;

    #[Vich\UploadableField(mapping: 'email_attachments', fileNameProperty: 'attachmentKey')]
    #[Assert\File(maxSize: '32M', mimeTypes: ['application/pdf'])]
    protected ?File $attachmentFile = null;

    /**
     * @var Collection<int,History>
     */
    #[MongoDB\EmbedMany(targetDocument: History::class)]
    protected Collection $history;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected DateTimeInterface $lastChecked;

    public static function forUser(Company $company, User $user): static
    {
        $email = parent::forUser($company, $user);

        if (null === $user->getContactEmail()) {
            throw new UnexpectedValueException('User contact email should not be null');
        }

        $email->setEmailAddress($user->getContactEmail());

        return $email;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function getMailjetId(): int
    {
        return $this->mailjetId;
    }

    public function setMailjetId(int $mailjetId): void
    {
        $this->mailjetId = $mailjetId;
    }

    public function getAttachmentKey(): ?string
    {
        return $this->attachmentKey;
    }

    public function setAttachmentKey(?string $attachmentKey): void
    {
        $this->attachmentKey = $attachmentKey;
    }

    /**
     * @return Collection<int,History>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    /**
     * @param Collection<int,History> $history
     */
    public function setHistory(Collection $history): void
    {
        $this->history = $history;
    }

    public function getLastChecked(): DateTimeInterface
    {
        return $this->lastChecked;
    }

    public function setLastChecked(DateTimeInterface $lastChecked): void
    {
        $this->lastChecked = $lastChecked;
    }

    public function getRecipient(): ?string
    {
        return $this->getEmailAddress();
    }

    public function getAttachmentFile(): ?File
    {
        return $this->attachmentFile;
    }

    public function setAttachmentFile(?File $attachmentFile): void
    {
        $this->attachmentFile = $attachmentFile;

        if (null !== $attachmentFile) {
            $this->updatedAt = new DateTimeImmutable();
            $this->createdAt = new DateTimeImmutable();
        }
    }
}
