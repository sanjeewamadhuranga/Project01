<?php

declare(strict_types=1);

namespace App\Infrastructure\Checklist;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, AbstractConstraint>
 */
final class CheckResults implements IteratorAggregate
{
    /**
     * @param AbstractConstraint[] $constraints
     */
    public function __construct(private readonly array $constraints)
    {
    }

    /**
     * @return AbstractConstraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->constraints);
    }

    public function allPassed(): bool
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->isChecked()) {
                return false;
            }
        }

        return true;
    }

    public function getPassedCount(): int
    {
        $valid = 0;

        foreach ($this->constraints as $constraint) {
            if ($constraint->isChecked()) {
                ++$valid;
            }
        }

        return $valid;
    }
}
