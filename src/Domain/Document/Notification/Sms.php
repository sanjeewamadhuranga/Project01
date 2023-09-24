<?php

declare(strict_types=1);

namespace App\Domain\Document\Notification;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Infrastructure\Repository\Notification\SmsRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use UnexpectedValueException;

#[MongoDB\Document(collection: 'comms_sms', repositoryClass: SmsRepository::class)]
class Sms extends AbstractNotification
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $phoneNumber;

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getTitle(): ?string
    {
        return null;
    }

    public function getRecipient(): ?string
    {
        return $this->getPhoneNumber();
    }

    public static function forUser(Company $company, User $user): static
    {
        $sms = parent::forUser($company, $user);

        if (null === $user->getMobile()) {
            throw new UnexpectedValueException('User mobile number should not be null');
        }

        $sms->setPhoneNumber($user->getMobile());

        return $sms;
    }
}
