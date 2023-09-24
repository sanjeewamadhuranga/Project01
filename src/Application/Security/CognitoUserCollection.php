<?php

declare(strict_types=1);

namespace App\Application\Security;

use IteratorAggregate;
use Traversable;

/**
 * Wraps an Iterator of {@see CognitoUser} together with the token to the next page.
 *
 * @implements IteratorAggregate<CognitoUser>
 */
final class CognitoUserCollection implements IteratorAggregate
{
    /**
     * @param iterable<CognitoUser> $users
     */
    public function __construct(private readonly iterable $users, private readonly ?string $nextToken)
    {
    }

    /**
     * @return iterable<CognitoUser>
     */
    public function getUsers(): iterable
    {
        return $this->users;
    }

    public function getNextToken(): ?string
    {
        return $this->nextToken;
    }

    /**
     * @return Traversable<CognitoUser>
     */
    public function getIterator(): Traversable
    {
        yield from $this->users;
    }
}
