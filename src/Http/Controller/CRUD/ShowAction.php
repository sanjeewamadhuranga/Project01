<?php

declare(strict_types=1);

namespace App\Http\Controller\CRUD;

use App\Application\Security\Permissions\Action;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait ShowAction
{
    #[Route('/{id}', name: 'show', priority: -1)]
    public function show(string $id): Response
    {
        $item = $this->find($id);
        $this->denyAccessUnlessGranted(sprintf('%s.%s', static::getPermissionPrefix(), Action::VIEW), $item);

        return $this->render(static::getTemplatePrefix().'/show.html.twig', ['item' => $item]);
    }
}
