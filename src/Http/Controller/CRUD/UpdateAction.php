<?php

declare(strict_types=1);

namespace App\Http\Controller\CRUD;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait UpdateAction
{
    use CreateOrUpdateTrait;

    #[Route('/{id}/edit', name: 'edit')]
    public function update(Request $request, string $id): Response
    {
        return $this->createOrUpdate($request, $id);
    }
}
