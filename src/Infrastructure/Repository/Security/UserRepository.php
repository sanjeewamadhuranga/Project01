<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Security;

use App\Application\Search\SearchableRepository;
use App\Application\Search\SearchQuery;
use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Doctrine\Criteria\Regex;
use App\Infrastructure\Repository\BaseRepository;
use Traversable;

/**
 * @extends BaseRepository<Administrator>
 *
 * @implements SearchableRepository<Administrator>
 */
class UserRepository extends BaseRepository implements SearchableRepository
{
    public static function objectClass(): string
    {
        return Administrator::class;
    }

    public function getUserByEmail(string $email): ?Administrator
    {
        return $this->findOneBy(['usernameCanonical' => $email]);
    }

    /**
     * @return iterable<int, Administrator>
     */
    public function search(SearchQuery $query): iterable
    {
        $search = $query->getQueryString();
        $searchRegex = Regex::contains($search);
        $qb = $this->createQueryBuilder();

        return $qb->addOr(
            $qb->expr()->field('id')->equals($search),
            $qb->expr()->field('email')->equals($searchRegex),
            $qb->expr()->field('username')->equals($searchRegex),
        )
            ->getQuery()
        ;
    }

    /**
     * @return Traversable<int, Administrator>
     */
    public function getUsersByRole(ManagerPortalRole $role): Traversable
    {
        return $this->notDeletedQueryBuilder()
            ->field('managerPortalRoles')->in([$role->getId()])
            ->getQuery()
        ;
    }
}
