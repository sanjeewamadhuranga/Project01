<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Flysystem;

use Cloudinary\Asset\AssetType;
use Cloudinary\Asset\BaseAsset;
use Cloudinary\Cloudinary;
use GuzzleHttp\Psr7;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;

class CloudinaryAdapter implements FilesystemAdapter
{
    private const EXTENSION_TYPES = [
        'txt' => AssetType::RAW,
        'htm' => AssetType::RAW,
        'html' => AssetType::RAW,
        'php' => AssetType::RAW,
        'css' => AssetType::RAW,
        'js' => AssetType::RAW,
        'json' => AssetType::RAW,
        'xml' => AssetType::RAW,
        'swf' => AssetType::RAW,
        'flv' => AssetType::VIDEO,

        // images
        'png' => AssetType::IMAGE,
        'jpe' => AssetType::IMAGE,
        'jpeg' => AssetType::IMAGE,
        'jpg' => AssetType::IMAGE,
        'gif' => AssetType::IMAGE,
        'bmp' => AssetType::IMAGE,
        'ico' => AssetType::IMAGE,
        'tiff' => AssetType::IMAGE,
        'tif' => AssetType::IMAGE,
        'svg' => AssetType::IMAGE,
        'svgz' => AssetType::IMAGE,

        // archives
        'zip' => AssetType::RAW,
        'rar' => AssetType::RAW,
        'exe' => AssetType::RAW,
        'msi' => AssetType::RAW,
        'cab' => AssetType::RAW,

        // video
        'mp3' => AssetType::VIDEO,
        'qt' => AssetType::VIDEO,
        'mov' => AssetType::VIDEO,
        'mp4' => AssetType::VIDEO,

        // adobe
        'pdf' => AssetType::RAW,
        'psd' => AssetType::IMAGE,
        'ai' => AssetType::RAW,
        'eps' => AssetType::RAW,
        'ps' => AssetType::RAW,

        // ms office
        'doc' => AssetType::RAW,
        'docx' => AssetType::RAW,
        'rtf' => AssetType::RAW,
        'xls' => AssetType::RAW,
        'xlsx' => AssetType::RAW,
        'ppt' => AssetType::RAW,
        'pptx' => AssetType::RAW,

        // open office
        'odt' => AssetType::RAW,
        'ods' => AssetType::RAW,
    ];

    public function __construct(private readonly Cloudinary $cloudinary)
    {
    }

    public function fileExists(string $path): bool
    {
        return true;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->upload($path, $contents);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->upload($path, $contents);
    }

    public function read(string $path): string
    {
        $readStream = $this->readStream($path);
        $contents = stream_get_contents($readStream);
        fclose($readStream);

        if (false === $contents) {
            throw UnableToReadFile::fromLocation($path);
        }

        return $contents;
    }

    public function readStream(string $path)
    {
        $resource = @fopen($this->getAsset($path)->toUrl(), 'rb');

        if (false === $resource) {
            throw UnableToReadFile::fromLocation($path);
        }

        return $resource;
    }

    public function delete(string $path): void
    {
        $this->cloudinary->adminApi()->deleteAssets($this->toPublicId($path), [
            AssetType::KEY => $this->getResourceType($path),
        ]);
    }

    public function deleteDirectory(string $path): void
    {
        $this->cloudinary->adminApi()->deleteAssetsByPrefix($path, [AssetType::KEY => AssetType::ALL]);
    }

    public function createDirectory(string $path, Config $config): void
    {
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path);
    }

    public function visibility(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::visibility($path);
    }

    public function mimeType(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::mimeType($path);
    }

    public function lastModified(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::lastModified($path);
    }

    public function fileSize(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::fileSize($path);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        return [];
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->cloudinary->uploadApi()->rename($this->toPublicId($source), $this->toPublicId($destination), [
            'type' => $this->getResourceType($source),
            'to_type' => $this->getResourceType($destination),
        ]);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        throw UnableToCopyFile::fromLocationTo($source, $destination);
    }

    private function getAsset(string $path): BaseAsset
    {
        $publicId = $this->toPublicId($path);

        return match ($this->getResourceType($path)) {
            AssetType::VIDEO => $this->cloudinary->video($publicId),
            AssetType::RAW => $this->cloudinary->raw($publicId),
            default => $this->cloudinary->image($publicId),
        };
    }

    /**
     * @param string|resource $contents
     *
     * @throws \Cloudinary\Api\Exception\ApiError
     */
    private function upload(string $path, mixed $contents): void
    {
        // @phpstan-ignore-next-line
        $this->cloudinary->uploadApi()->upload(Psr7\Utils::streamFor($contents), [
            'resource_type' => $this->getResourceType($path),
            'public_id' => $this->toPublicId($path),
        ]);
    }

    private function getResourceType(string $path): string
    {
        return self::EXTENSION_TYPES[pathinfo($path, PATHINFO_EXTENSION)] ?? AssetType::IMAGE;
    }

    private function toPublicId(string $key): string
    {
        if ('.' === pathinfo($key, PATHINFO_DIRNAME)) {
            return pathinfo($key, PATHINFO_FILENAME);
        }

        return sprintf('%s/%s', pathinfo($key, PATHINFO_DIRNAME), pathinfo($key, PATHINFO_FILENAME));
    }

    public function directoryExists(string $path): bool
    {
        throw UnableToCheckExistence::forLocation($path);
    }
}
