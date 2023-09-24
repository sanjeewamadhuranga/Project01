<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Storage\Flysystem;

use App\Infrastructure\Storage\Flysystem\CloudinaryAdapter;
use App\Tests\Unit\UnitTestCase;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Asset\AssetType;
use Cloudinary\Asset\BaseAsset;
use Cloudinary\Cloudinary;
use GuzzleHttp\Psr7;
use League\Flysystem\Config;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use PHPUnit\Framework\MockObject\MockObject;

use function PHPUnit\Framework\once;

class CloudinaryAdapterTest extends UnitTestCase
{
    private readonly Cloudinary&MockObject $cloudinary;

    private readonly CloudinaryAdapter $cloudinaryAdapter;

    protected function setUp(): void
    {
        $this->cloudinary = $this->createMock(Cloudinary::class);

        $this->cloudinaryAdapter = new CloudinaryAdapter($this->cloudinary);

        parent::setUp();
    }

    public function testWriteMethodUsesUploadApi(): void
    {
        $fileName = 'someFile';
        $path = $fileName.'.jpg';
        $content = 'someContent';

        $uploadApi = $this->createMock(UploadApi::class);
        $uploadApi->expects(once())->method('upload')->with(self::callback(fn ($stream) => $stream instanceof Psr7\Stream), [
            'resource_type' => AssetType::IMAGE,
            'public_id' => $fileName,
        ]);

        $this->cloudinary->method('uploadApi')->willReturn($uploadApi);

        $this->cloudinaryAdapter->write($path, $content, new Config());
    }

    public function testWriteStreamUsesUploadApi(): void
    {
        $fileName = 'myGreatMovie';
        $path = $fileName.'.mp4';
        $content = fopen(__FILE__, 'rb');
        self::assertNotFalse($content);

        $uploadApi = $this->createMock(UploadApi::class);
        $uploadApi->expects(once())->method('upload')->with(self::callback(fn ($stream) => $stream instanceof Psr7\Stream), [
            'resource_type' => AssetType::VIDEO,
            'public_id' => $fileName,
        ]);

        $this->cloudinary->method('uploadApi')->willReturn($uploadApi);

        $this->cloudinaryAdapter->writeStream($path, $content, new Config());
        fclose($content);
    }

    /**
     * @dataProvider readFilesProvider
     */
    public function testReadForFilesUsesCloudinary(string $fileName, string $extension, string $method, string $adapterMethod): void
    {
        $path = $fileName.'.'.$extension;

        $baseAsset = $this->createStub(BaseAsset::class);
        $baseAsset->method('toUrl')->willReturn(__FILE__);

        $this->cloudinary->expects(self::once())->method($method)->with($fileName)->willReturn($baseAsset);

        $this->cloudinaryAdapter->{$adapterMethod}($path); // @phpstan-ignore-line
    }

    public function testDeleteMethodUsesAdminApi(): void
    {
        $fileName = 'myContract';
        $path = $fileName.'.docx';

        $adminApi = $this->createMock(AdminApi::class);
        $adminApi->expects(self::once())->method('deleteAssets')->with($fileName, [
            AssetType::KEY => AssetType::RAW,
        ]);

        $this->cloudinary->method('adminApi')->willReturn($adminApi);
        $this->cloudinaryAdapter->delete($path);
    }

    public function testDeleteDirectoryMethodUsesAdminApi(): void
    {
        $path = '.';

        $adminApi = $this->createMock(AdminApi::class);
        $adminApi->expects(self::once())->method('deleteAssetsByPrefix')->with($path, [AssetType::KEY => AssetType::ALL]);

        $this->cloudinary->method('adminApi')->willReturn($adminApi);
        $this->cloudinaryAdapter->deleteDirectory($path);
    }

    public function testSetVisibilityMethodThrowsException(): void
    {
        $this->expectException(UnableToSetVisibility::class);

        $this->cloudinaryAdapter->setVisibility('.', 'invisible');
    }

    public function testThatListContentsReturnsEmptyArray(): void
    {
        self::assertSame([], $this->cloudinaryAdapter->listContents('.', true));
    }

    public function testMoveMethodUsesUploadApi(): void
    {
        $sourceFile = 'moveThisFile';
        $destinationFile = 'moveHereFile';
        $path = $sourceFile.'.jpg';
        $destination = $destinationFile.'.jpg';

        $uploadApi = $this->createMock(UploadApi::class);
        $uploadApi->expects(once())->method('rename')->with($sourceFile, $destinationFile, [
            'type' => AssetType::IMAGE,
            'to_type' => AssetType::IMAGE,
        ]);

        $this->cloudinary->method('uploadApi')->willReturn($uploadApi);
        $this->cloudinaryAdapter->move($path, $destination, new Config());
    }

    public function testThatCopyMethodThrowsException(): void
    {
        $this->expectException(UnableToCopyFile::class);

        $this->cloudinaryAdapter->copy(__FILE__, '../', new Config());
    }

    public function testThatDirectoryExistsMethodThrowsException(): void
    {
        $this->expectException(UnableToCheckExistence::class);

        $this->cloudinaryAdapter->directoryExists('.');
    }

    /**
     * @dataProvider methodsThatThrowsUnableToRetrieveMetadataExceptionProvider
     */
    public function testSomeMethodsThrowsUnableToRetrieveMetadataException(string $methodName): void
    {
        $this->expectException(UnableToRetrieveMetadata::class);

        $this->cloudinaryAdapter->{$methodName}('.'); // @phpstan-ignore-line
    }

    /**
     * @return iterable<array<int, string>>
     */
    public function readFilesProvider(): iterable
    {
        yield 'mp4 movie file for read' => ['movie', 'mp4', 'video', 'read'];
        yield 'txt readme file for read' => ['readme', 'txt', 'raw', 'read'];
        yield 'jpg image file for read' => ['image', 'jpg', 'image', 'read'];
        yield 'mp4 movie file for readStream' => ['movie', 'mp4', 'video', 'readStream'];
        yield 'txt readme file for readStream' => ['readme', 'txt', 'raw', 'readStream'];
        yield 'jpg image file for readStream' => ['image', 'jpg', 'image', 'readStream'];
    }

    /**
     * @return iterable<string[]>
     */
    public function methodsThatThrowsUnableToRetrieveMetadataExceptionProvider(): iterable
    {
        yield 'visibility method' => ['visibility'];
        yield 'mimeType method' => ['mimeType'];
        yield 'lastModified method' => ['lastModified'];
        yield 'fileSize method' => ['fileSize'];
    }
}
