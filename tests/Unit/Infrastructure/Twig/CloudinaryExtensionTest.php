<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Twig;

use App\Infrastructure\Twig\CloudinaryExtension;
use App\Tests\Unit\UnitTestCase;
use Cloudinary\Asset\Image;
use Cloudinary\Cloudinary;

class CloudinaryExtensionTest extends UnitTestCase
{
    public function testItRegistersFunctions(): void
    {
        $extension = new CloudinaryExtension($this->createStub(Cloudinary::class));
        $functions = $extension->getFunctions();

        self::assertCount(1, $functions);
        self::assertSame('cloudinary_url', $functions[0]->getName());
    }

    public function testItReturnsImageUrl(): void
    {
        $imageId = 'nice-picture-155';
        $imageUrl = 'https://example-image-url.com/nice-picture-155.jpg';
        $imageOptions = [];

        $image = $this->createMock(Image::class);
        $image->expects(self::once())->method('addTransformation')->with($imageOptions)->willReturn($image);
        $image->expects(self::once())->method('toUrl')->willReturn($imageUrl);

        $cloudinary = $this->createMock(Cloudinary::class);
        $cloudinary->expects(self::once())->method('image')->with($imageId)->willReturn($image);

        $extension = new CloudinaryExtension($cloudinary);

        self::assertSame($imageUrl, $extension->cloudinaryUrl($imageId, $imageOptions));
    }
}
