<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Infrastructure\DataGrid\Notifications\CustomEmailList;
use App\Infrastructure\DataGrid\Notifications\PushNotificationList;
use App\Infrastructure\DataGrid\Notifications\SmsList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications', name: 'notifications_')]
class NotificationsController extends BaseController
{
    public function __construct(
        private readonly SmsList $smsList,
        private readonly CustomEmailList $emailList,
        private readonly PushNotificationList $pushNotificationList
    ) {
    }

    #[Route('/{type}', name: 'index', requirements: ['type' => 'sms|email|push'])]
    public function __invoke(string $type): Response
    {
        $this->denyAccessUnlessGranted(Permission::MODULE_NOTIFICATION.$type.Action::VIEW);

        return $this->render(sprintf('notifications/%s.html.twig', $type));
    }

    #[Route('/{type}/list', name: 'list', requirements: ['type' => 'sms|email|push'])]
    public function list(string $type, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permission::MODULE_NOTIFICATION.$type.Action::VIEW);

        $list = match ($type) {
            'sms' => $this->smsList,
            'email' => $this->emailList,
            'push' => $this->pushNotificationList,
            default => throw $this->createNotFoundException(),
        };

        return $this->handleDataGrid($request, $list);
    }
}
