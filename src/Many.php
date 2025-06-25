<?php

declare(strict_types=1);

namespace Buildotter\Core;

final class Many
{
    /**
     * @template T
     *
     * @param class-string<Buildatable<T>> $class
     *
     * @return array<T>
     */
    public static function from(string $class, int|null $numberOfItems = null): array
    {
        $numberOfItems ??= static::numberBetween();

        $collection = [];
        for ($i = 0; $i < $numberOfItems; $i++) {
            $collection[] = $class::new()->build();
        }

        return $collection;
    }

    /**
     * @template T
     * @template TBuilder of Buildatable<T>
     *
     * @param class-string<TBuilder> $class
     *
     * @return array<TBuilder>
     */
    public static function toBuildFrom(string $class, int|null $numberOfItems = null): array
    {
        $numberOfItems ??= static::numberBetween();

        $collection = [];
        for ($i = 0; $i < $numberOfItems; $i++) {
            $collection[] = $class::new();
        }

        return $collection;
    }

    private static function numberBetween(int $min = 1, int $max = 100): int
    {
        $int1 = \min($min, $max);
        $int2 = \max($min, $max);

        return \mt_rand($int1, $int2);
    }
}
