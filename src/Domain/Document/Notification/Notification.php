<?php

declare(strict_types=1);

namespace App\Domain\Document\Notification;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;

interface Notification
{
    public function getCompany(): ?Company;

    public static function forUser(Company $company, User $user): self;

    public function getId(): ?string;

    public function isSent(): bool;

    public function setSent(bool $sent): void;

    public function getTitle(): ?string;

    public function hasTitle(): bool;

    public function getMessage(): ?string;

    public function getSub(): ?string;

    public function getRecipient(): ?string;

    /**
     * @return mixed[]
     */
    public function getMeta(): array;
}
