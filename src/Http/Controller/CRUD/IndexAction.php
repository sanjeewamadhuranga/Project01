<?php

declare(strict_types=1);

namespace App\Http\Controller\CRUD;

use App\Application\Security\Permissions\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait IndexAction
{
    #[Route('', name: 'index')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted(sprintf('%s.%s', static::getPermissionPrefix(), Action::VIEW));

        $params = [
            'component' => $this->getIndexComponent(),
            'props' => $this->getIndexComponentProps($request),
            'title' => $this->getIndexTitle(),
        ];

        if ($this->container->get('twig')->getLoader()->exists(static::getTemplatePrefix().'/index.html.twig')) {
            return $this->render(static::getTemplatePrefix().'/index.html.twig', $params);
        }

        return $this->render('common/crud/index.html.twig', $params);
    }

    /**
     * This method returns name of the web component which displays list on the index action.
     */
    public function getIndexComponent(): ?string
    {
        return str_replace(['.', '_'], '-', static::getKey()).'-list';
    }

    /**
     * This method returns translation key for.
     */
    public function getIndexTitle(): ?string
    {
        return static::getKey().'.title.list';
    }

    /**
     * @return array<string, mixed>
     */
    public function getIndexComponentProps(Request $request): array
    {
        return [];
    }
}
