<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Domain\Transaction\Status;

class TransactionFilters extends BasicFilters
{
    private ?string $id = null;

    private ?string $externalId = null;

    private ?string $buyerIdentifier = null;

    private ?string $paddedCardNumber = null;

    private ?string $providerBrandName = null;

    private ?string $securityWord = null;

    private ?string $customerReference = null;

    /** @var Status[] */
    private array $status = [];

    private ?int $amount = null;

    /** @var string[] */
    private array $currencies = [];

    /** @var string[] */
    private array $payCurrency = [];

    private ?DateRange $created = null;

    private ?DateRange $confirmed = null;

    private ?string $transactionReportId = null;

    /** @var string[] */
    private array $provider = [];

    /** @var string[] */
    private array $merchant = [];

    /** @var string[] */
    private array $locations = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Status[]
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param Status[] $status
     */
    public function setStatus(array $status): void
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
     * @return string[]
     */
    public function getMerchant(): array
    {
        return $this->merchant;
    }

    /**
     * @param string[] $merchant
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
    public function getProvider(): array
    {
        return $this->provider;
    }

    /**
     * @param string[] $provider
     */
    public function setProvider(array $provider): void
    {
        $this->provider = $provider;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getBuyerIdentifier(): ?string
    {
        return $this->buyerIdentifier;
    }

    public function setBuyerIdentifier(?string $buyerIdentifier): void
    {
        $this->buyerIdentifier = $buyerIdentifier;
    }

    public function getPaddedCardNumber(): ?string
    {
        return $this->paddedCardNumber;
    }

    public function setPaddedCardNumber(?string $paddedCardNumber): void
    {
        $this->paddedCardNumber = $paddedCardNumber;
    }

    public function getProviderBrandName(): ?string
    {
        return $this->providerBrandName;
    }

    public function setProviderBrandName(?string $providerBrandName): void
    {
        $this->providerBrandName = $providerBrandName;
    }

    public function getSecurityWord(): ?string
    {
        return $this->securityWord;
    }

    public function setSecurityWord(?string $securityWord): void
    {
        $this->securityWord = $securityWord;
    }

    public function getCustomerReference(): ?string
    {
        return $this->customerReference;
    }

    public function setCustomerReference(?string $customerReference): void
    {
        $this->customerReference = $customerReference;
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

    /**
     * @return string[]
     */
    public function getPayCurrency(): array
    {
        return $this->payCurrency;
    }

    /**
     * @param string[] $payCurrency
     */
    public function setPayCurrency(array $payCurrency): void
    {
        $this->payCurrency = $payCurrency;
    }

    public function getConfirmed(): ?DateRange
    {
        return $this->confirmed;
    }

    public function setConfirmed(?DateRange $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    public function setTransactionReportId(string $transactionReportId): void
    {
        $this->transactionReportId = $transactionReportId;
    }

    public function getTransactionReportId(): ?string
    {
        return $this->transactionReportId;
    }

    /**
     * @return string[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @param string[] $locations
     */
    public function setLocations(array $locations): void
    {
        $this->locations = $locations;
    }
}
