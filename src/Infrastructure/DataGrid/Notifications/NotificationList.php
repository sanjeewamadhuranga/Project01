<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Notifications;

use App\Application\DataGrid\Exception\InvalidFiltersProvidedException;
use App\Application\DataGrid\Filters\Filters;
use App\Domain\DataGrid\Filters\NotificationFilters;
use App\Domain\Document\Notification\AbstractNotification;
use App\Infrastructure\DataGrid\AbstractMongoDataGrid;
use App\Infrastructure\Doctrine\Criteria\Regex;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @template TDocument of AbstractNotification
 *
 * @extends AbstractMongoDataGrid<NotificationFilters, TDocument>
 */
class NotificationList extends AbstractMongoDataGrid
{
    /**
     * @param DocumentRepository<TDocument> $repository
     */
    public function __construct(private readonly DocumentRepository $repository)
    {
    }

    protected function getQuery(): Builder
    {
        return $this->repository->createQueryBuilder();
    }

    protected function applyFilters(Builder $query, Filters $filters): Builder
    {
        if (!$filters instanceof NotificationFilters) {
            throw new InvalidFiltersProvidedException(NotificationFilters::class, $filters);
        }

        if (null !== $filters->getSearch()) {
            $contains = Regex::contains($filters->getSearch());
            $query->addOr(
                $query->expr()->field('title')->equals($contains),
                $query->expr()->field('sub')->equals($contains),
                $query->expr()->field('company')->equals($contains),
                $query->expr()->field('message')->equals($contains),
                $query->expr()->field('id')->equals($filters->getSearch()),
            );
        }

        if (null !== $filters->getCompany()) {
            $query->field('company')->equals($filters->getCompany()->getId());
        }

        if (null !== $filters->getUserId()) {
            $query->field('sub')->equals($filters->getUserId());
        }

        return $query;
    }

    public function getFilterDto(): string
    {
        return NotificationFilters::class;
    }

    /**
     * @param AbstractNotification $item
     *
     * @return array<string, mixed>
     */
    public function transform(mixed $item, int|string $index): array
    {
        return [
            'id' => $item->getId(),
            'companyId' => $item->getCompany()?->getId(),
            'companyName' => $item->getCompany()?->__toString(),
            'sub' => $item->getSub(),
            'title' => $item->getTitle(),
            'message' => $item->getMessage(),
            'sent' => $item->isSent(),
            'metadata' => $item->getMeta(),
            'createdAt' => $item->getCreatedAt(),
        ];
    }

    public function getSortMap(): array
    {
        return [
            'id' => 'id',
            'createdAt' => 'id',
            'companyId' => 'company',
            'sub' => 'sub',
            'title' => 'title',
            'message' => 'message',
            'sent' => 'sent',
        ];
    }
}
