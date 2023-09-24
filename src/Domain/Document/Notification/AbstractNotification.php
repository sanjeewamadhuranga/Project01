<?php

declare(strict_types=1);

namespace App\Domain\Document\Notification;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use UnexpectedValueException;

#[MongoDB\MappedSuperclass]
abstract class AbstractNotification extends BaseDocument implements Notification
{
    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $sent = false;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $message = null;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected ?Company $company = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $sub = null;

    /**
     * @var mixed[]
     */
    #[MongoDB\Field(type: MongoDBType::RAW)]
    protected array $meta = [];

    /**
     * @throws UnexpectedValueException when $user->getSub() is null
     */
    public static function forUser(Company $company, User $user): static
    {
        // @phpstan-ignore-next-line (unsafe usage of static)
        $notification = new static();
        $notification->setCompany($company);

        if (null === $user->getSub()) {
            throw new UnexpectedValueException('user sub should not be null');
        }

        $notification->setSub($user->getSub());

        return $notification;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function hasTitle(): bool
    {
        return null !== $this->getTitle();
    }

    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getSub(): ?string
    {
        return $this->sub;
    }

    public function setSub(?string $sub): void
    {
        $this->sub = $sub;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param mixed[] $meta
     */
    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }
}
