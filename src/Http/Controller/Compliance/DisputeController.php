<?php

declare(strict_types=1);

namespace App\Http\Controller\Compliance;

use App\Application\Queue\Bus;
use App\Application\Queue\Commands\DisputeChargeback;
use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Domain\Compliance\DisputeState;
use App\Domain\Document\Compliance\Dispute;
use App\Domain\Document\Compliance\DisputeNote;
use App\Domain\Document\Transaction\Transaction;
use App\Domain\Transaction\TransactionCreateRequest;
use App\Http\Controller\BasicCrudController;
use App\Http\Controller\CRUD\CreateOrUpdateTrait;
use App\Http\Controller\CRUD\IndexAction;
use App\Http\Controller\CRUD\ListAction;
use App\Infrastructure\DataGrid\Compliance\DisputeList;
use App\Infrastructure\DataGrid\FixedItemsDataGrid;
use App\Infrastructure\Form\Compliance\CloseDisputeType;
use App\Infrastructure\Form\Compliance\DisputeNoteType;
use App\Infrastructure\Form\Compliance\DisputeSelectTransactionType;
use App\Infrastructure\Form\Compliance\DisputeType;
use App\Infrastructure\Form\Compliance\TransactionChargebackType;
use App\Infrastructure\Form\Transaction\TransactionReconfirmationType;
use App\Infrastructure\Repository\DisputeRepository;
use App\Infrastructure\Repository\Transaction\TransactionRepository;
use App\Infrastructure\Service\Client;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @extends BasicCrudController<Dispute>
 */
#[Route('/compliance/disputes', name: 'compliance_dispute_')]
class DisputeController extends BasicCrudController
{
    use IndexAction;
    use ListAction;
    use CreateOrUpdateTrait;

    public function __construct(private readonly DisputeList $list, private readonly DisputeRepository $repository, private readonly TransactionRepository $transactionRepository)
    {
    }

    protected static function getItemClass(): string
    {
        return Dispute::class;
    }

    public function getIndexComponent(): ?string
    {
        return 'dispute-list';
    }

    protected static function getKey(): string
    {
        return 'compliance.dispute';
    }

    protected function getList(): DisputeList // @phpstan-ignore-line
    {
        return $this->list;
    }

    #[Route('/create', name: 'create')]
    #[IsGranted(Permission::COMPLIANCE_DISPUTE.Action::CREATE)]
    public function create(Request $request): Response
    {
        $dispute = new Dispute();
        $form = $this->createForm(DisputeSelectTransactionType::class, $dispute)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute(
                'compliance_dispute_open_for_transaction',
                ['id' => $dispute->getTransaction()?->getId()]
            );
        }

        return $this->renderForm(
            'compliance/dispute/form.html.twig',
            [
                'form' => $form,
                'title' => $this->getCreateTitle(),
                'backUrl' => $this->getBackUrl($request),
                'subTitle' => $this->getSubTitle(),
            ]
        );
    }

    #[Route('/create/transaction/{id}', name: 'open_for_transaction')]
    #[IsGranted(Permission::COMPLIANCE_DISPUTE.Action::CREATE)]
    public function createFromTransaction(Transaction $transaction, Request $request): Response
    {
        $dispute = Dispute::forTransaction($transaction);
        $form = $this->createForm(DisputeType::class, $dispute)->handleRequest($request);
        $disputeFormParams = [
            'form' => $form,
            'item' => $dispute,
            'title' => $this->getCreateTitle(),
            'backUrl' => $this->getBackUrl($request),
            'subTitle' => $this->getSubTitle(),
        ];

        $existingDispute = $this->repository->getForTransactionOrReconfirmation($transaction);

        if (null !== $existingDispute) {
            return $this->renderForm(
                'compliance/dispute/form.html.twig',
                ['existingDispute' => $existingDispute, ...$disputeFormParams],
                new Response('', Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save($dispute);
            $this->addFlash('success', $this->trans('successfullyCreated'));

            return $this->redirectToRoute('compliance_dispute_show', ['id' => $dispute->getId()]);
        }

        return $this->renderForm('compliance/dispute/form.html.twig', $disputeFormParams);
    }

    #[Route('/{id}/create_reconfirmation', name: 'create_reconfirmation')]
    public function reconfirmation(Dispute $dispute, Request $request, Client $Client): Response
    {
        if (!$dispute->canCreateReconfirmation()) {
            $this->addFlash('danger', $this->trans('compliance.disputes.label.canNotCreateReconfirmation'));

            return $this->redirectToRoute('compliance_dispute_index');
        }

        $transactionCreateRequest = TransactionCreateRequest::fromDisputeForReconfirmation($dispute);
        $form = $this->createForm(TransactionReconfirmationType::class, $transactionCreateRequest)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $response = $Client->notify($transactionCreateRequest);
                /** @var Transaction $reconfirmation */
                $reconfirmation = $this->transactionRepository->find($response['id']);

                $dispute->proceedReconfirmation($reconfirmation);

                $this->getDoctrineMongoDb()->getManager()->persist($dispute);
                $this->getDoctrineMongoDb()->getManager()->flush();
                $this->addFlash('success', $this->trans('compliance.disputes.label.reconfirmationCreated'));
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('compliance_dispute_index');
        }

        return $this->renderForm('compliance/dispute/reconfirmation.html.twig', [
            'item' => $dispute,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/close_dispute', name: 'close_dispute', methods: ['POST'])]
    public function closeDispute(Request $request, Dispute $dispute): Response
    {
        if (DisputeState::CLOSED === $dispute->getState()) {
            $this->addFlash('warning', $this->trans('compliance.disputes.label.alreadyClosed'));

            return $this->redirectToRoute('compliance_dispute_show', ['id' => $dispute->getId()]);
        }

        $form = $this->createForm(CloseDisputeType::class, $dispute)->handleRequest($request);

        if (!$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $this->addFlash('danger', $error->getMessage());
            }

            return $this->redirectToRoute('compliance_dispute_show', ['id' => $dispute->getId()]);
        }

        if ($form->isSubmitted()) {
            $dispute->close();
            $this->getDoctrineMongoDb()->getManager()->persist($dispute);
            $this->getDoctrineMongoDb()->getManager()->flush();
            $this->addFlash('success', $this->trans('compliance.disputes.label.hasBeenClosed'));
        }

        return $this->redirectToRoute('compliance_dispute_index', ['id' => $dispute->getId()]);
    }

    #[Route('/{id}', name: 'show', priority: -1)]
    #[IsGranted(Permission::COMPLIANCE_DISPUTE.Action::VIEW)]
    public function show(Dispute $dispute): Response
    {
        return $this->render(static::getTemplatePrefix().'/show.html.twig', [
            'item' => $dispute,
            'closeDisputeForm' => $this->createForm(CloseDisputeType::class, $dispute, [
                'action' => $this->generateUrl('compliance_dispute_close_dispute', ['id' => $dispute->getId()]),
            ])->createView(),
        ]);
    }

    #[Route('/{id}/create-chargeback', name: 'create_chargeback')]
    #[IsGranted(Permission::COMPLIANCE_DISPUTE.Action::CREATE)]
    public function createChargeback(Dispute $dispute, Request $request, Client $Client, Bus $bus): Response
    {
        if (!$dispute->canCreateChargeback()) {
            $this->addFlash('danger', $this->trans('compliance.disputes.charge_back.already_exists'));

            return $this->redirectToRoute('compliance_dispute_index');
        }

        $transactionCreateRequest = TransactionCreateRequest::fromDisputeForChargeback($dispute);
        $form = $this->createForm(TransactionChargebackType::class, $transactionCreateRequest)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $response = $Client->notify($transactionCreateRequest);
                /** @var Transaction $chargeback */
                $chargeback = $this->transactionRepository->find($response['id']);
                $dispute->proceedChargeback($chargeback);

                $this->getDoctrineMongoDb()->getManager()->persist($dispute);
                $this->getDoctrineMongoDb()->getManager()->flush();

                $bus->dispatch(new DisputeChargeback($dispute, $chargeback));
                $this->addFlash('success', $this->trans('compliance.disputes.charge_back.created'));
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('compliance_dispute_index');
        }

        return $this->renderForm(static::getTemplatePrefix().'/create_chargeback.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/create-note', name: 'create_note')]
    #[IsGranted(Permission::COMPLIANCE_DISPUTE.Action::CREATE)]
    public function createNote(Dispute $dispute, Request $request): Response
    {
        $form = $this->createForm(DisputeNoteType::class, new DisputeNote())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dispute->addNote($form->getData());
            $dm = $this->getDoctrineMongoDb();
            $dm->getManager()->persist($dispute);
            $dm->getManager()->flush();

            $this->addFlash('success', $this->trans('compliance.dispute.create_notes.success'));

            return $this->redirectToRoute('compliance_dispute_show', ['id' => $dispute->getId()]);
        }

        return $this->renderForm('common/crud/form.html.twig', [
            'form' => $form,
            'item' => $dispute,
            'isCreate' => true,
            'title' => 'compliance.dispute.create_notes.title',
            'backUrl' => $this->getBackUrl($request),
            'subTitle' => $this->trans('compliance.dispute.create_notes.dispute_note'),
        ]);
    }

    #[Route('/{id}/list-notes', name: 'list_note'), IsGranted(Permission::COMPLIANCE_DISPUTE.Action::VIEW)]
    public function listNotes(Dispute $dispute, Request $request): Response
    {
        return $this->handleDataGrid($request, new FixedItemsDataGrid(
            $dispute->getActiveNotes(),
            static fn (DisputeNote $note, int $index) => [
                'id' => $note->getId(),
                'detail' => $note->getDetail(),
                'createdAt' => $note->getCreatedAt(),
                'email' => $note->getUser()->getEmail(),
            ]
        ));
    }

    /**
     * @return array{message: string}
     */
    #[Route('/{id}/delete/{noteId}', name: 'delete', methods: ['DELETE']), IsGranted(Permission::COMPLIANCE_DISPUTE.Action::DELETE)]
    public function deleteNotes(Dispute $dispute, string $noteId, DisputeRepository $disputeRepository): array
    {
        $dispute->getNote($noteId)?->setDeleted(true);
        $disputeRepository->save($dispute);

        return ['message' => $this->trans('note.deleteSuccess', [], 'messages')];
    }
}
