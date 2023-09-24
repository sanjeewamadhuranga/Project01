<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\Pagination;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Application\DataGrid\Pagination\CursorPaginationInfo;
use App\Application\Security\CognitoUser;
use App\Application\Security\CognitoUserCollection;
use App\Domain\DataGrid\Filters\FederatedIdentityFilters;
use App\Infrastructure\DataGrid\FederatedIdentityList;
use App\Infrastructure\Security\CognitoUserManager;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FederatedIdentityListTest extends UnitTestCase
{
    private CognitoUserManager&MockObject $cognitoUserManager;

    private FederatedIdentityList $list;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cognitoUserManager = $this->createMock(CognitoUserManager::class);
        $this->list = new FederatedIdentityList($this->cognitoUserManager);
    }

    public function testItListsIdentitiesWithoutFilters(): void
    {
        $this->cognitoUserManager->expects(self::once())
            ->method('listUsers')
            ->with(null, 50, 'first-token', true)
            ->willReturn(new CognitoUserCollection($this->getUsers(), 'next-token'));

        $users = $this->list->getData(new GridRequest(
            new FederatedIdentityFilters(),
            new Sorting(null, SortDirection::ASC),
            new Pagination(0, 50, 'first-token')
        ));

        self::assertCount(2, [...$users]);
        self::assertContainsOnlyInstancesOf(CognitoUser::class, $users);

        $paginationInfo = $users->getPaginationInfo();
        self::assertInstanceOf(CursorPaginationInfo::class, $paginationInfo);
        self::assertSame('first-token', $paginationInfo->getCurrentCursor());
        self::assertSame('next-token', $paginationInfo->getNextCursor());
    }

    /**
     * @dataProvider filterProvider
     */
    public function testItListsIdentitiesWithoutTokenAndWithFilters(FederatedIdentityFilters $filters, string $expectedQuery): void
    {
        $this->cognitoUserManager->expects(self::once())
            ->method('listUsers')
            ->with($expectedQuery, 25, null, true)
            ->willReturn(new CognitoUserCollection($this->getUsers(), 'test-token'));

        $users = $this->list->getData(new GridRequest($filters, new Sorting(null, SortDirection::ASC)));

        self::assertCount(2, [...$users]);
        self::assertContainsOnlyInstancesOf(CognitoUser::class, $users);

        $paginationInfo = $users->getPaginationInfo();
        self::assertInstanceOf(CursorPaginationInfo::class, $paginationInfo);
        self::assertNull($paginationInfo->getCurrentCursor());
        self::assertSame('test-token', $paginationInfo->getNextCursor());
    }

    /**
     * @return iterable<string, array{FederatedIdentityFilters, string}>
     */
    public function filterProvider(): iterable
    {
        $filters = new FederatedIdentityFilters();
        $filters->email = 'test@example.com';

        yield 'Email' => [$filters, 'email ^= "test@example.com"'];

        $filters = new FederatedIdentityFilters();
        $filters->email = 'test@example.com';
        $filters->mobile = '+44123456789';

        yield 'Email and phone number' => [$filters, 'email ^= "test@example.com" phone_number ^= "+44123456789"'];

        $filters = new FederatedIdentityFilters();
        $filters->sub = 'test"sub';

        yield 'Sub with a quote' => [$filters, 'sub ^= "test\"sub"'];
    }

    /**
     * @return CognitoUser[]
     */
    private function getUsers(): array
    {
        return [
            $this->getUser('1', 'John Doe', '+44123123123', 'john@doe.example.com', 'ACTIVE'),
            $this->getUser('2', 'John Doe', '+44123123123', 'john@doe.example.com', 'UNKNOWN'),
        ];
    }

    private function getUser(string $sub, string $name, string $email, string $phone, string $status): CognitoUser
    {
        $user = $this->createStub(CognitoUser::class);
        $user->method('getSub')->willReturn($sub);
        $user->method('getName')->willReturn($name);
        $user->method('getEmail')->willReturn($email);
        $user->method('getPhoneNumber')->willReturn($phone);
        $user->method('getStatus')->willReturn($status);

        return $user;
    }
}
