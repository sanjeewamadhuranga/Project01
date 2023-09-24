<?php

declare(strict_types=1);

namespace App\Http\Controller\CRUD;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait CreateAction
{
    use CreateOrUpdateTrait;

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        return $this->createOrUpdate($request);
    }
}
