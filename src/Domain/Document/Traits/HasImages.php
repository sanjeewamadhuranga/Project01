<?php

declare(strict_types=1);

namespace App\Domain\Document\Traits;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait HasImages
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $image = null;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $images = [];

    #[Vich\UploadableField(mapping: 'cloudinary_images', fileNameProperty: 'image')]
    #[Assert\File(maxSize: '32M', mimeTypes: ['image/png', 'image/jpeg'])]
    protected ?File $imageFile = null;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param string[] $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;

        if (property_exists($this, 'updatedAt')) { // @phpstan-ignore-line
            $this->updatedAt = new DateTime();
        }
    }
}
