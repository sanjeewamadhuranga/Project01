<?php

declare(strict_types=1);

namespace App\Http\Controller\CRUD;

use App\Application\Security\Permissions\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait ListAction
{
    #[Route('/list', name: 'list')]
    public function list(Request $request): Response
    {
        $this->denyAccessUnlessGranted(sprintf('%s.%s', static::getPermissionPrefix(), Action::VIEW));

        return $this->handleDataGrid($request, $this->getList());
    }
}
