<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Search;

use App\Application\Search\SearchProvider;
use App\Application\Search\SearchServiceLocator;
use App\Domain\Provider\CompanySearchProvider;
use App\Domain\Provider\CompanyUserProvider;
use App\Domain\Provider\TransactionSearchProvider;
use App\Domain\Provider\UserSearchProvider;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SearchServiceLocatorTest extends UnitTestCase
{
    public function testItMapsStringToProviders(): void
    {
        self::assertSame([
            'transaction' => TransactionSearchProvider::class,
            'administrator' => UserSearchProvider::class,
            'merchant' => CompanySearchProvider::class,
            'user' => CompanyUserProvider::class,
        ], SearchServiceLocator::getSubscribedServices());
    }

    public function testItThrowsExceptionWhenNoSearchProviderFound(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $location = $this->createStub(ContainerInterface::class);
        $location->method('has')->willReturn(false);

        (new SearchServiceLocator($location))->getSearchProvider('someNotExisting');
    }

    public function testItReturnsSearchProviderWhenCorrectSubjectProvided(): void
    {
        $searchProvider = $this->createStub(SearchProvider::class);

        $location = $this->createStub(ContainerInterface::class);
        $location->method('has')->willReturn(true);
        $location->method('get')->willReturn($searchProvider);

        $searchServiceLocator = new SearchServiceLocator($location);

        self::assertSame($searchProvider, $searchServiceLocator->getSearchProvider('test'));
    }
}
