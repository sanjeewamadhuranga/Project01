<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Transaction\TokenizationMethod;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class ResellerMetadata
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $merchantId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $paymentGatewayMerchantID = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $contractId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $paymentGatewayMerchantPWD = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $industryCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $microfilmCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $bankCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $bankBranch = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $payMode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $accountType = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $discountCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $holdCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $riskLevel = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $cifNumber = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $signatureType = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $unionPayMerchantId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $weChatPaySubmerchantId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $weChatPayOnboardingMcc = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $valitorMerchantId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $valitorMerchantCountry = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $valitorTerminalId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $valitorMerchantCity = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $valitorMerchantCategoryCode = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $valitorPCIApproved = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $valitorMerchantPostCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $valitorMerchantDescriptor = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $myPinPadMerchantId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $tapToPayTerminalId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $worldlineTerminalId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $mpgsGatewayPassword = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $mpgsMerchantId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $bmlMposVersion = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $useWalletAcquiring = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $countryCodeISO3 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?bool $mccDescription = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $unifiedPaymentForm = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $mpgsOnboarded = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $lankaQrCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $lankaQrMerchantId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $mpgsOnboardTime = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $commBankOnboarded = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $commBankTransactionMinAmount = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $commBankTransactionMaxAmount = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $commBankDailyAmountLimit = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $commBankMonthlyAmountLimit = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankAPIUserName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankAPIPassword = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankSdkUserId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankSdkUserPass = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankMid = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankTid = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankUserName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankPassword = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $commBankNationalId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $dialogServiceTypes = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $state = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $paymentName = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $paymentCurrency = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $linklevel = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $chainAccountNumber = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $cancellationDate = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $statusCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $bankCurrency = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $accountNumber = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $threeDSecureIndicator = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $tokenizationMerchantType = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $threeDSecureBypassClosedLoop = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $threeDSecureNotEnabledByPassClosedLoop = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $threeDSecureByPassExternal = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $threeDSecureNotEnabledByPassExternal = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $eciCavvValueAllowed = null;

    /**
     * @var array<int, string>
     */
    #[MongoDB\Field(type: MongoDBType::RAW)]
    protected ?array $myPinPadInstallation = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $showAddressOnReceipts = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $showSecurityWord = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $showConfetti = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $showRateExperience = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $weChatPayMiniProgramId = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $hasMppOnboardingSyncError = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $valitorOnboardingError = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $valitorContinueOnboarding = null;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::RAW)]
    protected ?array $serviceTypes = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $secondaryAcquiringEnabled = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $cyberSourceKey = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $cyberSourceMerchantId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $cyberSourceSharedSecret = null;

    #[MongoDB\Field(type: MongoDBType::STRING, enumType: TokenizationMethod::class)]
    protected ?TokenizationMethod $tokenizationMethod = null;

    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }

    public function setMerchantId(?string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    public function getPaymentGatewayMerchantID(): ?string
    {
        return $this->paymentGatewayMerchantID;
    }

    public function setPaymentGatewayMerchantID(?string $paymentGatewayMerchantID): void
    {
        $this->paymentGatewayMerchantID = $paymentGatewayMerchantID;
    }

    public function getContractId(): ?string
    {
        return $this->contractId;
    }

    public function setContractId(?string $contractId): void
    {
        $this->contractId = $contractId;
    }

    public function getPaymentGatewayMerchantPWD(): ?string
    {
        return $this->paymentGatewayMerchantPWD;
    }

    public function setPaymentGatewayMerchantPWD(?string $paymentGatewayMerchantPWD): void
    {
        $this->paymentGatewayMerchantPWD = $paymentGatewayMerchantPWD;
    }

    public function getIndustryCode(): ?string
    {
        return $this->industryCode;
    }

    public function setIndustryCode(?string $industryCode): void
    {
        $this->industryCode = $industryCode;
    }

    public function getMicrofilmCode(): ?string
    {
        return $this->microfilmCode;
    }

    public function setMicrofilmCode(?string $microfilmCode): void
    {
        $this->microfilmCode = $microfilmCode;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): void
    {
        $this->bankCode = $bankCode;
    }

    public function getBankBranch(): ?string
    {
        return $this->bankBranch;
    }

    public function setBankBranch(?string $bankBranch): void
    {
        $this->bankBranch = $bankBranch;
    }

    public function getPayMode(): ?string
    {
        return $this->payMode;
    }

    public function setPayMode(?string $payMode): void
    {
        $this->payMode = $payMode;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(?string $accountType): void
    {
        $this->accountType = $accountType;
    }

    public function getDiscountCode(): ?string
    {
        return $this->discountCode;
    }

    public function setDiscountCode(?string $discountCode): void
    {
        $this->discountCode = $discountCode;
    }

    public function getHoldCode(): ?string
    {
        return $this->holdCode;
    }

    public function setHoldCode(?string $holdCode): void
    {
        $this->holdCode = $holdCode;
    }

    public function getRiskLevel(): ?string
    {
        return $this->riskLevel;
    }

    public function setRiskLevel(?string $riskLevel): void
    {
        $this->riskLevel = $riskLevel;
    }

    public function getCifNumber(): ?string
    {
        return $this->cifNumber;
    }

    public function setCifNumber(?string $cifNumber): void
    {
        $this->cifNumber = $cifNumber;
    }

    public function getSignatureType(): ?string
    {
        return $this->signatureType;
    }

    public function setSignatureType(?string $signatureType): void
    {
        $this->signatureType = $signatureType;
    }

    public function getUnionPayMerchantId(): ?string
    {
        return $this->unionPayMerchantId;
    }

    public function setUnionPayMerchantId(?string $unionPayMerchantId): void
    {
        $this->unionPayMerchantId = $unionPayMerchantId;
    }

    public function getWeChatPaySubmerchantId(): ?string
    {
        return $this->weChatPaySubmerchantId;
    }

    public function setWeChatPaySubmerchantId(?string $weChatPaySubmerchantId): void
    {
        $this->weChatPaySubmerchantId = $weChatPaySubmerchantId;
    }

    public function getWeChatPayOnboardingMcc(): ?string
    {
        return $this->weChatPayOnboardingMcc;
    }

    public function setWeChatPayOnboardingMcc(?string $weChatPayOnboardingMcc): void
    {
        $this->weChatPayOnboardingMcc = $weChatPayOnboardingMcc;
    }

    public function hasValitorMerchantId(): bool
    {
        return null !== $this->valitorMerchantId && '' !== $this->valitorMerchantId;
    }

    public function getValitorMerchantId(): ?string
    {
        return $this->valitorMerchantId;
    }

    public function setValitorMerchantId(?string $valitorMerchantId): void
    {
        $this->valitorMerchantId = $valitorMerchantId;
    }

    public function getValitorMerchantCountry(): ?string
    {
        return $this->valitorMerchantCountry;
    }

    public function setValitorMerchantCountry(?string $valitorMerchantCountry): void
    {
        $this->valitorMerchantCountry = $valitorMerchantCountry;
    }

    public function hasValitorTerminalId(): bool
    {
        return null !== $this->valitorTerminalId && '' !== $this->valitorTerminalId;
    }

    public function getValitorTerminalId(): ?string
    {
        return $this->valitorTerminalId;
    }

    public function setValitorTerminalId(?string $valitorTerminalId): void
    {
        $this->valitorTerminalId = $valitorTerminalId;
    }

    public function getValitorMerchantCity(): ?string
    {
        return $this->valitorMerchantCity;
    }

    public function setValitorMerchantCity(?string $valitorMerchantCity): void
    {
        $this->valitorMerchantCity = $valitorMerchantCity;
    }

    public function getValitorMerchantCategoryCode(): ?string
    {
        return $this->valitorMerchantCategoryCode;
    }

    public function setValitorMerchantCategoryCode(?string $valitorMerchantCategoryCode): void
    {
        $this->valitorMerchantCategoryCode = $valitorMerchantCategoryCode;
    }

    public function getValitorPCIApproved(): ?bool
    {
        return $this->valitorPCIApproved;
    }

    public function setValitorPCIApproved(?bool $valitorPCIApproved): void
    {
        $this->valitorPCIApproved = $valitorPCIApproved;
    }

    public function getValitorMerchantPostCode(): ?string
    {
        return $this->valitorMerchantPostCode;
    }

    public function setValitorMerchantPostCode(?string $valitorMerchantPostCode): void
    {
        $this->valitorMerchantPostCode = $valitorMerchantPostCode;
    }

    public function getValitorMerchantDescriptor(): ?string
    {
        return $this->valitorMerchantDescriptor;
    }

    public function setValitorMerchantDescriptor(?string $valitorMerchantDescriptor): void
    {
        $this->valitorMerchantDescriptor = $valitorMerchantDescriptor;
    }

    public function getMyPinPadMerchantId(): ?string
    {
        return $this->myPinPadMerchantId;
    }

    public function setMyPinPadMerchantId(?string $myPinPadMerchantId): void
    {
        $this->myPinPadMerchantId = $myPinPadMerchantId;
    }

    public function getTapToPayTerminalId(): ?string
    {
        return $this->tapToPayTerminalId;
    }

    public function setTapToPayTerminalId(?string $tapToPayTerminalId): void
    {
        $this->tapToPayTerminalId = $tapToPayTerminalId;
    }

    public function getLankaQrMerchantId(): ?string
    {
        return $this->lankaQrMerchantId;
    }

    public function setLankaQrMerchantId(?string $lankaQrMerchantId): void
    {
        $this->lankaQrMerchantId = $lankaQrMerchantId;
    }

    public function getWorldlineTerminalId(): ?string
    {
        return $this->worldlineTerminalId;
    }

    public function setWorldlineTerminalId(?string $worldlineTerminalId): void
    {
        $this->worldlineTerminalId = $worldlineTerminalId;
    }

    public function getMpgsGatewayPassword(): ?string
    {
        return $this->mpgsGatewayPassword;
    }

    public function setMpgsGatewayPassword(?string $mpgsGatewayPassword): void
    {
        $this->mpgsGatewayPassword = $mpgsGatewayPassword;
    }

    public function getMpgsMerchantId(): ?string
    {
        return $this->mpgsMerchantId;
    }

    public function setMpgsMerchantId(?string $mpgsMerchantId): void
    {
        $this->mpgsMerchantId = $mpgsMerchantId;
    }

    public function getBmlMposVersion(): ?string
    {
        return $this->bmlMposVersion;
    }

    public function setBmlMposVersion(?string $bmlMposVersion): void
    {
        $this->bmlMposVersion = $bmlMposVersion;
    }

    public function isUseWalletAcquiring(): ?bool
    {
        return $this->useWalletAcquiring;
    }

    public function setUseWalletAcquiring(?bool $useWalletAcquiring): void
    {
        $this->useWalletAcquiring = $useWalletAcquiring;
    }

    public function getCountryCodeISO3(): ?string
    {
        return $this->countryCodeISO3;
    }

    public function setCountryCodeISO3(?string $countryCodeISO3): void
    {
        $this->countryCodeISO3 = $countryCodeISO3;
    }

    public function isMccDescription(): ?bool
    {
        return $this->mccDescription;
    }

    public function setMccDescription(?bool $mccDescription): void
    {
        $this->mccDescription = $mccDescription;
    }

    public function isUnifiedPaymentForm(): ?bool
    {
        return $this->unifiedPaymentForm;
    }

    public function setUnifiedPaymentForm(?bool $unifiedPaymentForm): void
    {
        $this->unifiedPaymentForm = $unifiedPaymentForm;
    }

    public function isMpgsOnboarded(): bool
    {
        if (null === $this->mpgsOnboarded) {
            return false;
        }

        return $this->mpgsOnboarded;
    }

    public function setMpgsOnboarded(?bool $mpgsOnboarded): void
    {
        $this->mpgsOnboarded = $mpgsOnboarded;
    }

    public function getLankaQrCode(): ?string
    {
        return $this->lankaQrCode;
    }

    public function setLankaQrCode(?string $lankaQrCode): void
    {
        $this->lankaQrCode = $lankaQrCode;
    }

    public function getMpgsOnboardTime(): ?string
    {
        return $this->mpgsOnboardTime;
    }

    public function setMpgsOnboardTime(?string $mpgsOnboardTime): void
    {
        $this->mpgsOnboardTime = $mpgsOnboardTime;
    }

    public function getCommBankOnboarded(): ?bool
    {
        return $this->commBankOnboarded;
    }

    public function setCommBankOnboarded(?bool $commBankOnboarded): void
    {
        $this->commBankOnboarded = $commBankOnboarded;
    }

    public function getCommBankTransactionMinAmount(): ?int
    {
        return $this->commBankTransactionMinAmount;
    }

    public function setCommBankTransactionMinAmount(?int $commBankTransactionMinAmount): void
    {
        $this->commBankTransactionMinAmount = $commBankTransactionMinAmount;
    }

    public function getCommBankTransactionMaxAmount(): ?int
    {
        return $this->commBankTransactionMaxAmount;
    }

    public function setCommBankTransactionMaxAmount(?int $commBankTransactionMaxAmount): void
    {
        $this->commBankTransactionMaxAmount = $commBankTransactionMaxAmount;
    }

    public function getCommBankDailyAmountLimit(): ?int
    {
        return $this->commBankDailyAmountLimit;
    }

    public function setCommBankDailyAmountLimit(?int $commBankDailyAmountLimit): void
    {
        $this->commBankDailyAmountLimit = $commBankDailyAmountLimit;
    }

    public function getCommBankMonthlyAmountLimit(): ?int
    {
        return $this->commBankMonthlyAmountLimit;
    }

    public function setCommBankMonthlyAmountLimit(?int $commBankMonthlyAmountLimit): void
    {
        $this->commBankMonthlyAmountLimit = $commBankMonthlyAmountLimit;
    }

    public function getCommBankAPIUserName(): ?string
    {
        return $this->commBankAPIUserName;
    }

    public function setCommBankAPIUserName(?string $commBankAPIUserName): void
    {
        $this->commBankAPIUserName = $commBankAPIUserName;
    }

    public function getCommBankAPIPassword(): ?string
    {
        return $this->commBankAPIPassword;
    }

    public function setCommBankAPIPassword(?string $commBankAPIPassword): void
    {
        $this->commBankAPIPassword = $commBankAPIPassword;
    }

    public function getCommBankSdkUserId(): ?string
    {
        return $this->commBankSdkUserId;
    }

    public function setCommBankSdkUserId(?string $commBankSdkUserId): void
    {
        $this->commBankSdkUserId = $commBankSdkUserId;
    }

    public function getCommBankSdkUserPass(): ?string
    {
        return $this->commBankSdkUserPass;
    }

    public function setCommBankSdkUserPass(?string $commBankSdkUserPass): void
    {
        $this->commBankSdkUserPass = $commBankSdkUserPass;
    }

    public function getCommBankMid(): ?string
    {
        return $this->commBankMid;
    }

    public function setCommBankMid(?string $commBankMid): void
    {
        $this->commBankMid = $commBankMid;
    }

    public function getCommBankTid(): ?string
    {
        return $this->commBankTid;
    }

    public function setCommBankTid(?string $commBankTid): void
    {
        $this->commBankTid = $commBankTid;
    }

    public function getCommBankUserName(): ?string
    {
        return $this->commBankUserName;
    }

    public function setCommBankUserName(?string $commBankUserName): void
    {
        $this->commBankUserName = $commBankUserName;
    }

    public function getCommBankPassword(): ?string
    {
        return $this->commBankPassword;
    }

    public function setCommBankPassword(?string $commBankPassword): void
    {
        $this->commBankPassword = $commBankPassword;
    }

    public function getCommBankNationalId(): ?string
    {
        return $this->commBankNationalId;
    }

    public function setCommBankNationalId(?string $commBankNationalId): void
    {
        $this->commBankNationalId = $commBankNationalId;
    }

    public function getDialogServiceTypes(): ?string
    {
        return $this->dialogServiceTypes;
    }

    public function setDialogServiceTypes(?string $dialogServiceTypes): void
    {
        $this->dialogServiceTypes = $dialogServiceTypes;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getPaymentName(): ?string
    {
        return $this->paymentName;
    }

    public function setPaymentName(?string $paymentName): void
    {
        $this->paymentName = $paymentName;
    }

    public function getPaymentCurrency(): ?int
    {
        return $this->paymentCurrency;
    }

    public function setPaymentCurrency(?int $paymentCurrency): void
    {
        $this->paymentCurrency = $paymentCurrency;
    }

    public function getLinklevel(): ?string
    {
        return $this->linklevel;
    }

    public function setLinklevel(?string $linklevel): void
    {
        $this->linklevel = $linklevel;
    }

    public function getChainAccountNumber(): ?string
    {
        return $this->chainAccountNumber;
    }

    public function setChainAccountNumber(?string $chainAccountNumber): void
    {
        $this->chainAccountNumber = $chainAccountNumber;
    }

    public function getCancellationDate(): ?string
    {
        return $this->cancellationDate;
    }

    public function setCancellationDate(?string $cancellationDate): void
    {
        $this->cancellationDate = $cancellationDate;
    }

    public function getStatusCode(): ?string
    {
        return $this->statusCode;
    }

    public function setStatusCode(?string $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getBankCurrency(): ?string
    {
        return $this->bankCurrency;
    }

    public function setBankCurrency(?string $bankCurrency): void
    {
        $this->bankCurrency = $bankCurrency;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    public function getThreeDSecureIndicator(): ?bool
    {
        return $this->threeDSecureIndicator;
    }

    public function setThreeDSecureIndicator(?bool $threeDSecureIndicator): void
    {
        $this->threeDSecureIndicator = $threeDSecureIndicator;
    }

    public function getTokenizationMerchantType(): ?string
    {
        return $this->tokenizationMerchantType;
    }

    public function setTokenizationMerchantType(?string $tokenizationMerchantType): void
    {
        $this->tokenizationMerchantType = $tokenizationMerchantType;
    }

    public function getThreeDSecureBypassClosedLoop(): ?bool
    {
        return $this->threeDSecureBypassClosedLoop;
    }

    public function setThreeDSecureBypassClosedLoop(?bool $threeDSecureBypassClosedLoop): void
    {
        $this->threeDSecureBypassClosedLoop = $threeDSecureBypassClosedLoop;
    }

    public function getThreeDSecureNotEnabledByPassClosedLoop(): ?bool
    {
        return $this->threeDSecureNotEnabledByPassClosedLoop;
    }

    public function setThreeDSecureNotEnabledByPassClosedLoop(?bool $threeDSecureNotEnabledByPassClosedLoop): void
    {
        $this->threeDSecureNotEnabledByPassClosedLoop = $threeDSecureNotEnabledByPassClosedLoop;
    }

    public function getThreeDSecureByPassExternal(): ?bool
    {
        return $this->threeDSecureByPassExternal;
    }

    public function setThreeDSecureByPassExternal(?bool $threeDSecureByPassExternal): void
    {
        $this->threeDSecureByPassExternal = $threeDSecureByPassExternal;
    }

    public function getThreeDSecureNotEnabledByPassExternal(): ?bool
    {
        return $this->threeDSecureNotEnabledByPassExternal;
    }

    public function setThreeDSecureNotEnabledByPassExternal(?bool $threeDSecureNotEnabledByPassExternal): void
    {
        $this->threeDSecureNotEnabledByPassExternal = $threeDSecureNotEnabledByPassExternal;
    }

    public function getEciCavvValueAllowed(): ?bool
    {
        return $this->eciCavvValueAllowed;
    }

    public function setEciCavvValueAllowed(?bool $eciCavvValueAllowed): void
    {
        $this->eciCavvValueAllowed = $eciCavvValueAllowed;
    }

    /**
     * @return array<int, string>
     */
    public function getMyPinPadInstallation(): array|null
    {
        return $this->myPinPadInstallation;
    }

    /**
     * @param array<int, string> $myPinPadInstallation
     */
    public function setMyPinPadInstallation(array|null $myPinPadInstallation): void
    {
        $this->myPinPadInstallation = $myPinPadInstallation;
    }

    public function getShowAddressOnReceipts(): ?bool
    {
        return $this->showAddressOnReceipts;
    }

    public function setShowAddressOnReceipts(?bool $showAddressOnReceipts): void
    {
        $this->showAddressOnReceipts = $showAddressOnReceipts;
    }

    public function getShowSecurityWord(): ?bool
    {
        return $this->showSecurityWord;
    }

    public function setShowSecurityWord(?bool $showSecurityWord): void
    {
        $this->showSecurityWord = $showSecurityWord;
    }

    public function getShowConfetti(): ?bool
    {
        return $this->showConfetti;
    }

    public function setShowConfetti(?bool $showConfetti): void
    {
        $this->showConfetti = $showConfetti;
    }

    public function getShowRateExperience(): ?bool
    {
        return $this->showRateExperience;
    }

    public function setShowRateExperience(?bool $showRateExperience): void
    {
        $this->showRateExperience = $showRateExperience;
    }

    public function getWeChatPayMiniProgramId(): ?string
    {
        return $this->weChatPayMiniProgramId;
    }

    public function setWeChatPayMiniProgramId(?string $weChatPayMiniProgramId): void
    {
        $this->weChatPayMiniProgramId = $weChatPayMiniProgramId;
    }

    public function getHasMppOnboardingSyncError(): ?bool
    {
        return $this->hasMppOnboardingSyncError;
    }

    public function setHasMppOnboardingSyncError(?bool $hasMppOnboardingSyncError): void
    {
        $this->hasMppOnboardingSyncError = $hasMppOnboardingSyncError;
    }

    public function getValitorOnboardingError(): ?bool
    {
        return $this->valitorOnboardingError;
    }

    public function setValitorOnboardingError(?bool $valitorOnboardingError): void
    {
        $this->valitorOnboardingError = $valitorOnboardingError;
    }

    public function getValitorContinueOnboarding(): ?bool
    {
        return $this->valitorContinueOnboarding;
    }

    public function setValitorContinueOnboarding(?bool $valitorContinueOnboarding): void
    {
        $this->valitorContinueOnboarding = $valitorContinueOnboarding;
    }

    /**
     * @return string[]|null
     */
    public function getServiceTypes(): ?array
    {
        return $this->serviceTypes;
    }

    /**
     * @param string[]|null $serviceTypes
     */
    public function setServiceTypes(?array $serviceTypes): void
    {
        $this->serviceTypes = $serviceTypes;
    }

    public function getSecondaryAcquiringEnabled(): ?bool
    {
        return $this->secondaryAcquiringEnabled;
    }

    public function setSecondaryAcquiringEnabled(?bool $secondaryAcquiringEnabled): void
    {
        $this->secondaryAcquiringEnabled = $secondaryAcquiringEnabled;
    }

    public function getCyberSourceKey(): ?string
    {
        return $this->cyberSourceKey;
    }

    public function setCyberSourceKey(?string $cyberSourceKey): void
    {
        $this->cyberSourceKey = $cyberSourceKey;
    }

    public function getCyberSourceMerchantId(): ?string
    {
        return $this->cyberSourceMerchantId;
    }

    public function setCyberSourceMerchantId(?string $cyberSourceMerchantId): void
    {
        $this->cyberSourceMerchantId = $cyberSourceMerchantId;
    }

    public function getCyberSourceSharedSecret(): ?string
    {
        return $this->cyberSourceSharedSecret;
    }

    public function setCyberSourceSharedSecret(?string $cyberSourceSharedSecret): void
    {
        $this->cyberSourceSharedSecret = $cyberSourceSharedSecret;
    }

    public function getTokenizationMethod(): ?TokenizationMethod
    {
        return $this->tokenizationMethod;
    }

    public function setTokenizationMethod(?TokenizationMethod $tokenizationMethod): void
    {
        $this->tokenizationMethod = $tokenizationMethod;
    }
}
