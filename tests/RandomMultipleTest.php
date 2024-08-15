<?php

declare(strict_types=1);

namespace Buildotter\Tests\Core;

use Buildotter\Core\BuildableWithArgUnpacking;
use Buildotter\Core\Buildatable;
use Buildotter\Core\RandomMultiple;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertContainsOnlyInstancesOf;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertLessThanOrEqual;

class RandomMultipleTest extends TestCase
{
    public function test_it_builds_multiple_instances_from_builder(): void
    {
        $bazs = RandomMultiple::from(
            BazBuilder::class,
            $n = random()->numberBetween(2, 5),
        );

        assertCount($n, $bazs);
        assertContainsOnlyInstancesOf(Baz::class, $bazs);
    }

    public function test_it_builds_multiple_instances_from_builder_with_random_number_of_items_per_default(): void
    {
        $bazs = RandomMultiple::from(BazBuilder::class);

        $numberOfItems = \count($bazs);
        assertGreaterThanOrEqual(1, $numberOfItems);
        assertLessThanOrEqual(100, $numberOfItems);
        assertContainsOnlyInstancesOf(Baz::class, $bazs);
    }

    public function test_it_returns_multiple_builder_instances_ready_to_be_built(): void
    {
        $bazs = RandomMultiple::toBuildFrom(
            BazBuilder::class,
            $n = random()->numberBetween(2, 5),
        );

        assertCount($n, $bazs);
        assertContainsOnlyInstancesOf(BazBuilder::class, $bazs);
    }

    public function test_it_returns_multiple_builder_instances_ready_to_be_built_with_random_number_of_items_per_default(): void
    {
        $bazs = RandomMultiple::toBuildFrom(BazBuilder::class);

        $numberOfItems = \count($bazs);
        assertGreaterThanOrEqual(1, $numberOfItems);
        assertLessThanOrEqual(100, $numberOfItems);
        assertContainsOnlyInstancesOf(BazBuilder::class, $bazs);
    }
}

final class Baz
{
    public function __construct(
        public string $value,
    ) {}
}

/**
 * @implements Buildatable<Baz>
 */
final class BazBuilder implements Buildatable
{
    use BuildableWithArgUnpacking;

    public function __construct(
        public string $value,
    ) {}

    public static function random(): static
    {
        $random = random();

        return new static($random->text());
    }

    public function build(): Baz
    {
        return new Baz($this->value);
    }
}
