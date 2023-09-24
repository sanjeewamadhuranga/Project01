<?php

declare(strict_types=1);

namespace App\Infrastructure\Menu;

use App\Domain\Settings\Config;
use IteratorAggregate;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Traversable;

/**
 * @implements IteratorAggregate<MenuItem>
 */
abstract class AbstractMenu implements IteratorAggregate, MenuInterface
{
    public function __construct(
        protected readonly Config $config,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    protected function isGranted(string $permission): bool
    {
        return $this->authorizationChecker->isGranted($permission);
    }

    /**
     * @return Traversable<MenuItem>
     */
    public function getIterator(): Traversable
    {
        yield from $this->getItems();
    }
}
