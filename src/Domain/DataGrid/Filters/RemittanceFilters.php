<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Domain\Document\Company\Company;
use App\Domain\Transaction\RemittanceStatus;

class RemittanceFilters extends BasicFilters
{
    private ?RemittanceStatus $status = null;

    private ?int $amount = null;

    /** @var string[] */
    private array $currencies = [];

    private ?DateRange $created = null;

    /** @var Company[] */
    private array $merchant = [];

    private ?bool $paid = null;

    private ?string $externalId = null;

    private ?string $description = null;

    public function getStatus(): ?RemittanceStatus
    {
        return $this->status;
    }

    public function setStatus(?RemittanceStatus $status): void
    {
        $this->status = $status;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return Company[]
     */
    public function getMerchant(): array
    {
        return $this->merchant;
    }

    /**
     * @param Company[] $merchant
     */
    public function setMerchant(array $merchant): void
    {
        $this->merchant = $merchant;
    }

    public function getCreated(): ?DateRange
    {
        return $this->created;
    }

    public function setCreated(?DateRange $created): void
    {
        $this->created = $created;
    }

    /**
     * @return string[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    /**
     * @param string[] $currencies
     */
    public function setCurrencies(array $currencies): void
    {
        $this->currencies = $currencies;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(?bool $paid): void
    {
        $this->paid = $paid;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
