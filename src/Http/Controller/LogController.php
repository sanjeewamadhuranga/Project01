<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Infrastructure\DataGrid\LogList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/configuration/logs', name: 'configuration_log_')]
#[IsGranted(Permission::CONFIGURATION_LOGS.Action::VIEW)]
class LogController extends BaseController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->renderForm('configuration/logs/index.html.twig');
    }

    #[Route('/list', name: 'list')]
    public function list(LogList $list, Request $request): Response
    {
        return $this->handleDataGrid($request, $list);
    }
}
