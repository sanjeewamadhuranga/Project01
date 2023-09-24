<?php

declare(strict_types=1);

namespace App\Http\Controller\CRUD;

use App\Application\Security\Permissions\Action;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait DeleteAction
{
    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        $item = $this->find($id);
        $this->denyAccessUnlessGranted(sprintf('%s.%s', static::getPermissionPrefix(), Action::DELETE), $item);
        $item->setDeleted(true);
        $this->getDoctrineMongoDb()->getManager()->persist($item);
        $this->getDoctrineMongoDb()->getManager()->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
