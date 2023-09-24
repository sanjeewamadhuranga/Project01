<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Cloudinary\Cloudinary;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/uploads', name: 'uploads_')]
class UploadsController extends BaseController
{
    #[Route('/images/cloudinary/{path}', 'cloudinary_image')]
    public function cloudinaryImage(string $path, Cloudinary $cloudinary): Response
    {
        /** @var UriInterface $url */
        $url = $cloudinary->image($path)->toUrl();

        return new RedirectResponse((string) $url);
    }
}
