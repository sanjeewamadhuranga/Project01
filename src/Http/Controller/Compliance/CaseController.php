<?php

declare(strict_types=1);

namespace App\Http\Controller\Compliance;

use App\Application\Compliance\AssignType;
use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\ComplianceCase;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Http\Controller\BasicCrudController;
use App\Http\Controller\CRUD\CreateAction;
use App\Http\Controller\CRUD\DeleteAction;
use App\Http\Controller\CRUD\IndexAction;
use App\Http\Controller\CRUD\ListAction;
use App\Infrastructure\DataGrid\Compliance\CaseList;
use App\Infrastructure\DataGrid\Compliance\CaseTransactionsList;
use App\Infrastructure\Form\Compliance\CaseApproveFlowType;
use App\Infrastructure\Form\Compliance\CaseAssignType;
use App\Infrastructure\Form\Compliance\CaseReviewType;
use App\Infrastructure\Form\Compliance\PayoutBlockType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @extends BasicCrudController<PayoutBlock>
 */
#[Route('/compliance/case', name: 'compliance_case_')]
class CaseController extends BasicCrudController
{
    use IndexAction;
    use ListAction;
    use DeleteAction;
    use CreateAction;

    public function __construct(private readonly CaseList $list)
    {
    }

    public function getIndexTitle(): string
    {
        return 'menu.compliance';
    }

    /**
     * @return array<string, mixed>
     */
    public function getIndexComponentProps(Request $request): array
    {
        return [
            'onlyme' => $request->get('onlyMe'),
        ];
    }

    protected function getList(): CaseList // @phpstan-ignore-line
    {
        return $this->list;
    }

    #[Route('/{id}', name: 'show', priority: -1)]
    public function show(PayoutBlock $case, Request $request): Response
    {
        $this->denyAccessUnlessGranted(sprintf('%s.%s', static::getPermissionPrefix(), Action::VIEW), $case);

        if ($this->isGranted(ComplianceCase::APPROVE, $case)) {
            $approveForm = $this->createForm(CaseApproveFlowType::class, $case->getCaseFlow())->handleRequest($request);

            if ($approveForm->isSubmitted() && $approveForm->isValid()) {
                return $this->handleApprove($case);
            }
        }

        if ($this->isGranted(ComplianceCase::REVIEW, $case)) {
            $reviewForm = $this->createForm(CaseReviewType::class, $case)->handleRequest($request);

            if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
                return $this->handleReview($case);
            }
        }

        if ($this->isGranted(ComplianceCase::ASSIGN_REVIEW, $case)) {
            $assignReviewForm = $this->createForm(CaseAssignType::class, $case)->handleRequest($request);
            if ($assignReviewForm->isSubmitted() && $assignReviewForm->isValid()) {
                if (AssignType::SELF === $assignReviewForm['assignTo']?->getData()) {
                    $case->setHandler($this->getUser());
                }
                $this->getDoctrineMongoDb()->getManager()->flush();

                return $this->redirectToCase($case);
            }
        }

        if ($this->isGranted(ComplianceCase::ASSIGN_APPROVE, $case)) {
            $assignApproveForm = $this->createForm(CaseAssignType::class, $case)->handleRequest($request);
            if ($assignApproveForm->isSubmitted() && $assignApproveForm->isValid()) {
                if (AssignType::SELF === $assignApproveForm['assignTo']?->getData()) {
                    $case->setApprover($this->getUser());
                }
                $this->getDoctrineMongoDb()->getManager()->flush();

                return $this->redirectToCase($case);
            }
        }

        if ($this->isGranted(ComplianceCase::ASSUME_REVIEW, $case)) {
            $assumeReviewForm = $this->createForm(CaseAssignType::class, $case)->handleRequest($request);

            if ($assumeReviewForm->isSubmitted() && $assumeReviewForm->isValid()) {
                return $this->assumeReview($case);
            }
        }

        if ($this->isGranted(ComplianceCase::ASSUME_APPROVE, $case)) {
            $assumeApproveForm = $this->createForm(CaseAssignType::class, $case)->handleRequest($request);
            if ($assumeApproveForm->isSubmitted() && $assumeApproveForm->isValid()) {
                return $this->assumeApprove($case);
            }
        }

        return $this->renderForm('compliance/case/show.html.twig', [
            'item' => $case,
            'reviewForm' => $reviewForm ?? null,
            'approveForm' => $approveForm ?? null,
            'assignReviewForm' => $assignReviewForm ?? null,
            'assignApproveForm' => $assignApproveForm ?? null,
            'assumeReviewForm' => $assumeReviewForm ?? null,
            'assumeApproveForm' => $assumeApproveForm ?? null,
        ]);
    }

    #[Route('/{id}/transactions', name: 'transactions')]
    public function transactions(PayoutBlock $case, Request $request): Response
    {
        /** @var DocumentManager $dm */
        $dm = $this->getDoctrineMongoDb()->getManager();

        return $this->handleDataGrid($request, new CaseTransactionsList($dm, $case));
    }

    protected static function getFormType(): string
    {
        return PayoutBlockType::class;
    }

    protected static function getItemClass(): string
    {
        return PayoutBlock::class;
    }

    protected static function getKey(): string
    {
        return 'compliance.case';
    }

    private function redirectToCase(PayoutBlock $case): Response
    {
        return $this->redirectToRoute('compliance_case_show', ['id' => $case->getId()]);
    }

    private function handleApprove(PayoutBlock $case): Response
    {
        $case->setApproved(true);
        $this->getDoctrineMongoDb()->getManager()->flush();

        return $this->redirectToCase($case);
    }

    private function handleReview(PayoutBlock $case): Response
    {
        $case->setReviewed(true);
        $this->getDoctrineMongoDb()->getManager()->flush();

        return $this->redirectToCase($case);
    }

    private function assumeApprove(PayoutBlock $case): Response
    {
        $case->setApprover($this->getUser());
        $this->getDoctrineMongoDb()->getManager()->flush();

        return $this->redirectToCase($case);
    }

    private function assumeReview(PayoutBlock $case): Response
    {
        $case->setHandler($this->getUser());
        $this->getDoctrineMongoDb()->getManager()->flush();

        return $this->redirectToCase($case);
    }
}
