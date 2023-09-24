<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[MongoDB\EmbeddedDocument]
#[Uploadable]
class Branding
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $logo = null;

    #[UploadableField(mapping: 'cloudinary_images', fileNameProperty: 'logo')]
    protected ?File $logoFile = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $heroImage = null;

    #[UploadableField(mapping: 'cloudinary_images', fileNameProperty: 'heroImage')]
    protected ?File $imageFile = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $emailHeader = null;

    #[UploadableField(mapping: 'cloudinary_images', fileNameProperty: 'emailHeader')]
    protected ?File $emailHeaderFile = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $emailBackgroundColor = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $emailTextColor = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $font = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $storeFront = null;

    #[UploadableField(mapping: 'cloudinary_images', fileNameProperty: 'storeFront')]
    protected ?File $storeFrontFile = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $storeInterior = null;

    #[UploadableField(mapping: 'cloudinary_images', fileNameProperty: 'storeInterior')]
    protected ?File $storeInteriorFile = null;

    #[MongoDB\EmbedOne(targetDocument: MapPoint::class)]
    protected ?MapPoint $mapPoint = null;

    #[MongoDB\EmbedOne(targetDocument: MapPoint::class)]
    protected MapPoint $bankAccount;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $termsAndConditions = null;

    #[Assert\Email]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $paymentLinkCC = null;

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function setLogoFile(File $logoFile): void
    {
        $this->logoFile = $logoFile;
    }

    public function getHeroImage(): ?string
    {
        return $this->heroImage;
    }

    public function setHeroImage(string $heroImage): void
    {
        $this->heroImage = $heroImage;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getEmailHeader(): ?string
    {
        return $this->emailHeader;
    }

    public function setEmailHeader(string $emailHeader): void
    {
        $this->emailHeader = $emailHeader;
    }

    public function getEmailHeaderFile(): ?File
    {
        return $this->emailHeaderFile;
    }

    public function setEmailHeaderFile(File $emailHeaderFile): void
    {
        $this->emailHeaderFile = $emailHeaderFile;
    }

    public function getEmailBackgroundColor(): ?string
    {
        return $this->emailBackgroundColor;
    }

    public function setEmailBackgroundColor(string $emailBackgroundColor): void
    {
        $this->emailBackgroundColor = $emailBackgroundColor;
    }

    public function getEmailTextColor(): ?string
    {
        return $this->emailTextColor;
    }

    public function setEmailTextColor(string $emailTextColor): void
    {
        $this->emailTextColor = $emailTextColor;
    }

    public function getFont(): ?string
    {
        return $this->font;
    }

    public function setFont(string $font): void
    {
        $this->font = $font;
    }

    public function getStoreFront(): ?string
    {
        return $this->storeFront;
    }

    public function setStoreFront(string $storeFront): void
    {
        $this->storeFront = $storeFront;
    }

    public function getStoreFrontFile(): ?File
    {
        return $this->storeFrontFile;
    }

    public function setStoreFrontFile(File $storeFrontFile): void
    {
        $this->storeFrontFile = $storeFrontFile;
    }

    public function getStoreInterior(): ?string
    {
        return $this->storeInterior;
    }

    public function setStoreInterior(string $storeInterior): void
    {
        $this->storeInterior = $storeInterior;
    }

    public function getStoreInteriorFile(): ?File
    {
        return $this->storeInteriorFile;
    }

    public function setStoreInteriorFile(File $storeInteriorFile): void
    {
        $this->storeInteriorFile = $storeInteriorFile;
    }

    public function getMapPoint(): ?MapPoint
    {
        return $this->mapPoint;
    }

    public function setMapPoint(MapPoint $mapPoint): void
    {
        $this->mapPoint = $mapPoint;
    }

    public function getBankAccount(): MapPoint
    {
        return $this->bankAccount;
    }

    public function setBankAccount(MapPoint $bankAccount): void
    {
        $this->bankAccount = $bankAccount;
    }

    public function getTermsAndConditions(): ?string
    {
        return $this->termsAndConditions;
    }

    public function setTermsAndConditions(string $termsAndConditions): void
    {
        $this->termsAndConditions = $termsAndConditions;
    }

    public function getPaymentLinkCC(): ?string
    {
        return $this->paymentLinkCC;
    }

    public function setPaymentLinkCC(?string $paymentLinkCC): void
    {
        $this->paymentLinkCC = $paymentLinkCC;
    }
}
