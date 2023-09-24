<?php

declare(strict_types=1);

namespace App\Http\Controller\CRUD;

use App\Application\Security\Permissions\Action;
use App\Domain\Document\Interfaces\Activeable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait StatusAction
{
    #[Route('/{id}/status/{active}', name: 'toggle_status', methods: ['PUT'])]
    public function changeStatus(string $id, bool $active): Response
    {
        /** @var Activeable $item */
        $item = $this->find($id);
        $this->denyAccessUnlessGranted(sprintf('%s.%s', static::getPermissionPrefix(), Action::EDIT), $item);

        $item->setActive($active);
        $this->getDoctrineMongoDb()->getManager()->persist($item);
        $this->getDoctrineMongoDb()->getManager()->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
