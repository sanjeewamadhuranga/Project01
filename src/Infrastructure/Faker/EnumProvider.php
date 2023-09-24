<?php

declare(strict_types=1);

namespace App\Infrastructure\Faker;

use Faker\Provider\Base;
use InvalidArgumentException;
use UnitEnum;

class EnumProvider
{
    /**
     * @param array<string, class-string<UnitEnum>> $enumMapping
     */
    public function __construct(private array $enumMapping = [])
    {
        foreach ($enumMapping as $enumAlias => $enumClass) {
            $this->ensureEnumClass($enumClass);
            $this->enumMapping[$enumAlias] = $enumClass;
        }
    }

    /**
     * @throws InvalidArgumentException When $enumClassAlias is not a valid alias
     */
    public function randomEnum(string $enumClassOrAlias): UnitEnum
    {
        /** @var class-string<UnitEnum> $class */
        $class = $this->enumMapping[$enumClassOrAlias] ?? $enumClassOrAlias;
        $this->ensureEnumClass($class);

        return Base::randomElement($class::cases());
    }

    /**
     * Make sure that $enumClass is a proper Enum class. Throws exception otherwise.
     *
     * @param class-string $enumClass
     *
     * @throws InvalidArgumentException When $enumClass is not a class or is not a proper Enum
     */
    private function ensureEnumClass(string $enumClass): void
    {
        if (!is_a($enumClass, UnitEnum::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a proper enum class', $enumClass));
        }
    }
}
