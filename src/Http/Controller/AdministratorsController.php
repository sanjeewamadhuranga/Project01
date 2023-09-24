<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\DataGrid\DataGrid;
use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Domain\Document\Security\Administrator;
use App\Domain\Event\User\TwoFactorDisableEvent;
use App\Domain\Transformer\UserTransformer;
use App\Http\Controller\CRUD\IndexAction;
use App\Http\Controller\CRUD\ListAction;
use App\Http\Controller\CRUD\ShowAction;
use App\Http\Controller\CRUD\UpdateAction;
use App\Infrastructure\DataGrid\DynamicMongoDataGrid;
use App\Infrastructure\Form\Security\UserType;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Infrastructure\Security\PasswordGenerator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Goodby\CSV\Export\Standard\Collection\CallbackCollection;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @extends BasicCrudController<Administrator>
 */
#[Route('/administrators', name: 'administrators_')]
class AdministratorsController extends BasicCrudController
{
    use IndexAction;
    use ListAction;
    use ShowAction;
    use UpdateAction;

    public function __construct(private readonly UserTransformer $userTransformer)
    {
    }

    #[Route('/create', name: 'create')]
    #[IsGranted(Permission::MODULE_ADMINISTRATORS.Action::CREATE)]
    public function create(Request $request): Response
    {
        $user = new Administrator();
        $form = $this->createForm(static::getFormType(), $user, ['validation_groups' => ['Default', 'create']])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tempPassword = PasswordGenerator::generate(16);
            $user->setPlainPassword($tempPassword);
            $this->getDoctrineMongoDb()->getManager()->persist($user);
            $this->getDoctrineMongoDb()->getManager()->flush();
            $this->addFlash('success', $this->trans('successfullyCreated'));

            return $this->render(static::getTemplatePrefix().'/created.html.twig', ['user' => $user, 'tempPassword' => $tempPassword]);
        }

        return $this->renderForm(
            'common/crud/form.html.twig',
            [
                'form' => $form,
                'isCreate' => true,
                'title' => 'administrators.title.create',
                'backUrl' => $this->generateUrl('administrators_index'),
                'subTitle' => 'administrators.title.subtitle',
            ]
        );
    }

    #[Route('/{id}/enable', name: 'enable', methods: ['PUT'])]
    #[IsGranted(Permission::MODULE_ADMINISTRATORS.Action::ENABLE)]
    public function enable(Administrator $user): Response
    {
        $user->setEnabled(true);
        $this->getDoctrineMongoDb()->getManager()->persist($user);
        $this->getDoctrineMongoDb()->getManager()->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/disable', name: 'disable', methods: ['PUT'])]
    #[IsGranted(Permission::MODULE_ADMINISTRATORS.Action::DISABLE)]
    public function disable(Administrator $user): Response
    {
        $user->setEnabled(false);
        $this->getDoctrineMongoDb()->getManager()->persist($user);
        $this->getDoctrineMongoDb()->getManager()->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/2fa/reset', name: 'reset_2fa', methods: ['PUT'])]
    #[IsGranted(Permission::MODULE_ADMINISTRATORS.Action::EDIT)]
    public function reset2fa(Administrator $user, EventDispatcherInterface $eventDispatcher): Response
    {
        $user->reset2fa();
        $this->getDoctrineMongoDb()->getManager()->persist($user);
        $this->getDoctrineMongoDb()->getManager()->flush();

        $eventDispatcher->dispatch(new TwoFactorDisableEvent($user, Administrator::MFA_GOOGLE));
        $eventDispatcher->dispatch(new TwoFactorDisableEvent($user, Administrator::MFA_SMS));

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/suspension/reset', name: 'reset_suspension', methods: ['PUT'])]
    #[IsGranted(Permission::MODULE_ADMINISTRATORS.Action::EDIT)]
    public function resetSuspension(Administrator $user): Response
    {
        $user->resetSuspension();
        $this->getDoctrineMongoDb()->getManager()->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/export', name: 'export')]
    #[IsGranted(Permission::MODULE_ADMINISTRATORS.Action::DOWNLOAD)]
    public function export(UserRepository $repository): Response
    {
        $collection = new CallbackCollection($repository->createQueryBuilder()->getQuery(), static fn (Administrator $user) => [
                $user->getUserIdentifier(),
                $user->getEmail(),
                $user->isEnabled() ? 'Enabled' : 'Disabled',
                implode(' | ', $user->getManagerPortalRoles()->toArray()),
                $user->getLastLogin()?->format(DATE_ATOM) ?? '-',
                $user->getLocale(),
                $user->getGoogleId(),
            ]);

        return new StreamedResponse(static function () use ($collection): void {
            $config = (new ExporterConfig())
                ->setColumnHeaders([
                    'Username',
                    'Email',
                    'Enabled?',
                    'Assigned_Role',
                    'Last_Login',
                    'Locale',
                    'GoogleId',
                ]);
            $exporter = new Exporter($config);
            $exporter->export('php://output', $collection);
        }, Response::HTTP_OK, [
            'Content-Disposition' => HeaderUtils::makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'Administrator_Report.csv'
            ),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    #[IsGranted(Permission::MODULE_ADMINISTRATORS.Action::DELETE)]
    public function delete(Administrator $user): Response
    {
        $user->scrambleEmail();
        $user->setDeleted(true);
        $user->setEnabled(false);

        $this->getDoctrineMongoDb()->getManager()->persist($user);
        $this->getDoctrineMongoDb()->getManager()->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    protected static function getFormType(): string
    {
        return UserType::class;
    }

    protected static function getItemClass(): string
    {
        return Administrator::class;
    }

    protected static function getKey(): string
    {
        return 'administrators';
    }

    /**
     * @return DynamicMongoDataGrid<Administrator>
     */
    protected function getList(): DataGrid
    {
        /** @var DocumentManager $dm */
        $dm = $this->getDoctrineMongoDb()->getManager();
        $transformer = $this->userTransformer;

        return new DynamicMongoDataGrid($dm, static::getItemClass(), static fn (Administrator $user) => $transformer->transform($user));
    }

    public function getIndexComponent(): ?string
    {
        return 'administrator-list';
    }
}
