<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Company;

use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Domain\Company\UserStatus;
use App\Domain\DataGrid\Filters\BasicFilters;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Domain\Transformer\CompanyUserTransformer;
use App\Infrastructure\DataGrid\Company\UserList;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class UserListTest extends UnitTestCase
{
    /**
     * @param list<string> $expectedUserIds
     *
     * @dataProvider userProvider
     */
    public function testItFindsUsersByQuery(?string $query, ?Sorting $sorting, array $expectedUserIds): void
    {
        $users = $this->getUsers();
        $company = new Company();
        $company->setUsers(new ArrayCollection($users));
        $filters = new BasicFilters();
        $filters->setSearch($query);
        $list = new UserList($company, new CompanyUserTransformer());

        $data = $list->getData(new GridRequest($filters, $sorting ?? new Sorting(null, SortDirection::ASC)));

        $items = $data->getItems();
        self::assertInstanceOf(ArrayCollection::class, $items);
        // @phpstan-ignore-next-line
        self::assertSame($expectedUserIds, $items->map(static fn (User $user) => $user->getSub())->toArray());
    }

    /**
     * @return iterable<string, array{string|null, Sorting|null, string[]}>
     */
    public function userProvider(): iterable
    {
        yield 'No search query returns all users' => [null, null, ['1', '2', '3']];
    }

    /**
     * @return array<int, User>
     */
    private function getUsers(): array
    {
        return [
            $this->getUser('1', 'John', 'Doe', 'test@example.com', '+44123456', UserStatus::ACTIVE),
            $this->getUser('2', 'John', 'Wick', 'test2@example.com', '+44123456', UserStatus::ACTIVE),
            $this->getUser('3', 'Simon', 'Doe', 'test3@example.com', '+6599998888', UserStatus::SUSPENDED),
        ];
    }

    private function getUser(string $sub, string $firstName, string $lastName, string $email, string $phone, UserStatus $status): User
    {
        $user = new User();
        $user->setSub($sub);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setContactEmail($email);
        $user->setMobile($phone);
        $user->setState($status);

        return $user;
    }
}
