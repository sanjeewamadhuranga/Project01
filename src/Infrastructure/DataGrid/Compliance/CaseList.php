<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Compliance;

use App\Application\DataGrid\Exception\InvalidFiltersProvidedException;
use App\Application\DataGrid\Filters\Filters;
use App\Domain\Compliance\PayoutBlockStatus;
use App\Domain\DataGrid\Filters\PayoutBlockFilters;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\DataGrid\AbstractMongoDataGrid;
use App\Infrastructure\Repository\Company\CompanyRepository;
use App\Infrastructure\Repository\PayoutBlockRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @extends AbstractMongoDataGrid<PayoutBlockFilters, PayoutBlock>
 */
class CaseList extends AbstractMongoDataGrid
{
    public function __construct(
        private readonly PayoutBlockRepository $payoutBlockRepository,
        private readonly CompanyRepository $companyRepository,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    protected function getQuery(): Builder
    {
        return $this->payoutBlockRepository->notDeletedQueryBuilder();
    }

    public function getFilterDto(): string
    {
        return PayoutBlockFilters::class;
    }

    /**
     * @param PayoutBlock $item
     *
     * @return array<string, mixed>
     */
    public function transform(mixed $item, int|string $index): array
    {
        return [
            'id' => $item->getId(),
            'company' => [
                'id' => $item->getCompany()->getId(),
                'name' => $item->getCompany()->getRegisteredName(),
                'riskProfile' => $item->getCompany()->getRiskProfile()?->getCode(),
                'riskProfileId' => $item->getCompany()->getRiskProfile()?->getId(),
            ],
            'createdAt' => $item->getCreatedAt(),
            'reason' => $item->getReason(),
            'approved' => $item->isApproved(),
            'reviewed' => $item->isReviewed(),
            'approver' => $item->getApprover()?->getEmail(),
            'handler' => $item->getHandler()?->getEmail(),
            'status' => $item->getStatus(),
            'email' => $item->getEmail(),
        ];
    }

    public function applyFilters(Builder $query, Filters $filters): Builder
    {
        if (!$filters instanceof PayoutBlockFilters) {
            throw new InvalidFiltersProvidedException(PayoutBlockFilters::class, $filters);
        }

        $query = $this->applyStatusFilter($query, $filters->getStatus());

        $user = $this->tokenStorage->getToken()?->getUser();
        if (!$user instanceof Administrator) {
            throw new AccessDeniedException();
        }

        if ($filters->isOnlyMe()) {
            $query = $this->payoutBlockRepository->filterByUser($query, $user);
        }

        if (null !== $filters->getSearch()) {
            $search = $filters->getSearch();
            $company = $this->companyRepository->findByIdOrRegisteredName($search);

            if (null !== $company) {
                $query->addOr(
                    $query->expr()->field('company')->references($company)
                );
            }

            $query->addOr(
                $query->expr()->field('id')->equals($search),
            );
        }

        return $query;
    }

    public function getSortMap(): array
    {
        return [
            'id' => 'id',
            'createdAt' => 'createdAt',
            'company' => 'company',
        ];
    }

    private function applyStatusFilter(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            PayoutBlockStatus::OPEN->value => $query->addAnd(
                $query->expr()->field('reviewed')->equals(false),
                $query->expr()->field('approved')->equals(false),
                $query->expr()->field('handler')->equals(null),
            ),
            PayoutBlockStatus::IN_REVIEW->value => $query->addAnd(
                $query->expr()->field('reviewed')->equals(false),
                $query->expr()->field('handler')->notEqual(null)
            ),
            PayoutBlockStatus::IN_APPROVAL->value => $query->addAnd(
                $query->expr()->field('reviewed')->equals(true),
                $query->expr()->field('approved')->equals(false),
            ),
            PayoutBlockStatus::CLOSED->value => $query->addAnd(
                $query->expr()->field('reviewed')->equals(true),
                $query->expr()->field('approved')->equals(true)
            ),
            default => $query
        };
    }
}
