<?php

declare(strict_types=1);

namespace App\Http\Controller\CRUD;

use App\Application\Security\Permissions\Action;
use App\Domain\Document\BaseDocument;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait CreateOrUpdateTrait
{
    protected function createOrUpdate(Request $request, ?string $id = null): Response
    {
        $isCreate = null === $id;
        $item = $isCreate ? $this->getNewInstance() : $this->find($id);

        $this->denyAccessUnlessGranted(
            sprintf('%s.%s', static::getPermissionPrefix(), $isCreate ? Action::CREATE : Action::EDIT),
            $item
        );
        $form = $this->createForm(
            static::getFormType(),
            $item,
            ['validation_groups' => ['Default', $isCreate ? 'create' : 'edit']]
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrineMongoDb()->getManager()->persist($item);
            $this->getDoctrineMongoDb()->getManager()->flush();
            $this->addFlash('success', $this->trans($isCreate ? 'successfullyCreated' : 'successfullyUpdated'));

            return $this->getRedirect($item);
        }

        $title = $isCreate ? $this->getCreateTitle() : $this->getUpdateTitle();

        $params = ['form' => $form, 'item' => $item, 'isCreate' => $isCreate, 'title' => $title, 'backUrl' => $this->getBackUrl($request), 'subTitle' => $this->getSubTitle()];

        if ($this->container->get('twig')->getLoader()->exists(static::getTemplatePrefix().'/form.html.twig')) {
            return $this->renderForm(static::getTemplatePrefix().'/form.html.twig', $params);
        }

        return $this->renderForm('common/crud/form.html.twig', $params);
    }

    /**
     * This redirect is used after processed form action (create/update) which can be overwritten.
     */
    protected function getRedirect(BaseDocument $item): RedirectResponse
    {
        return $this->redirectToRoute(static::getRoutePrefix().'_index');
    }

    /**
     * This method returns translation key of title for create view.
     */
    public function getCreateTitle(): ?string
    {
        return static::getKey().'.title.create';
    }

    /**
     * This method returns translation key of title for update view.
     */
    public function getUpdateTitle(): ?string
    {
        return static::getKey().'.title.update';
    }

    /**
     * This method returns translation key of subTitle for update view.
     */
    public function getSubTitle(): ?string
    {
        return static::getKey().'.title.subtitle';
    }

    /**
     * This method returns url to go back from form view.
     */
    public function getBackUrl(Request $request): ?string
    {
        return $this->generateUrl(static::getRoutePrefix().'_index');
    }
}
