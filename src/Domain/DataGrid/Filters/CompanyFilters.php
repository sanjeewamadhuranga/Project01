<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Domain\Company\Status;
use App\Domain\Document\Circles;

class CompanyFilters extends BasicFilters
{
    private ?string $searchMerchant = null;

    private ?string $tradingName = null;

    private ?string $merchantId = null;

    private ?string $userEmail = null;

    private ?string $userPhone = null;

    private ?string $phone = null;

    private ?string $address = null;

    private ?Status $status = null;

    private ?string $currency = null;

    private ?bool $includeDeleted = null;

    private ?bool $onlyWithUsers = null;

    private ?bool $onlyWithTradingName = null;

    private ?Circles $circle = null;

    private ?DateRange $merchantAdded = null;

    /** @var string[] */
    private array $subscriptionPlans = [];

    /** @var string[] */
    private array $providers = [];

    public function getSearchMerchant(): ?string
    {
        return $this->searchMerchant;
    }

    public function setSearchMerchant(?string $searchMerchant): void
    {
        $this->searchMerchant = $searchMerchant;
    }

    public function getTradingName(): ?string
    {
        return $this->tradingName;
    }

    public function setTradingName(?string $tradingName): void
    {
        $this->tradingName = $tradingName;
    }

    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }

    public function setMerchantId(?string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(?string $userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    public function getIncludeDeleted(): ?bool
    {
        return $this->includeDeleted;
    }

    public function setIncludeDeleted(?bool $includeDeleted): void
    {
        $this->includeDeleted = $includeDeleted;
    }

    public function getOnlyWithUsers(): ?bool
    {
        return $this->onlyWithUsers;
    }

    public function setOnlyWithUsers(?bool $onlyWithUsers): void
    {
        $this->onlyWithUsers = $onlyWithUsers;
    }

    public function getOnlyWithTradingName(): ?bool
    {
        return $this->onlyWithTradingName;
    }

    public function setOnlyWithTradingName(?bool $onlyWithTradingName): void
    {
        $this->onlyWithTradingName = $onlyWithTradingName;
    }

    public function getCircle(): ?Circles
    {
        return $this->circle;
    }

    public function setCircle(?Circles $circle): void
    {
        $this->circle = $circle;
    }

    /**
     * @return string[]
     */
    public function getSubscriptionPlans(): array
    {
        return $this->subscriptionPlans;
    }

    /**
     * @param string[] $subscriptionPlans
     */
    public function setSubscriptionPlans(array $subscriptionPlans): void
    {
        $this->subscriptionPlans = $subscriptionPlans;
    }

    /**
     * @return string[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @param string[] $providers
     */
    public function setProviders(array $providers): void
    {
        $this->providers = $providers;
    }

    public function getMerchantAdded(): ?DateRange
    {
        return $this->merchantAdded;
    }

    public function setMerchantAdded(?DateRange $merchantAdded): void
    {
        $this->merchantAdded = $merchantAdded;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getUserPhone(): ?string
    {
        return $this->userPhone;
    }

    public function setUserPhone(?string $userPhone): void
    {
        $this->userPhone = $userPhone;
    }
}
