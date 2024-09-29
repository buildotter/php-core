<?php

declare(strict_types=1);

namespace Buildotter\Tests\Core;

use Buildotter\Core\BuildableWithArgUnpacking;
use Buildotter\Core\BuildableWithArray;
use Buildotter\Core\Buildatable;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

final class BuildatableTest extends TestCase
{
    public function test_it_is_buildatable_with_array(): void
    {
        $fooBuiltWithArray = FooBuilderWithArray::new()
            ->named($text = random()->name())
            ->with(['number' => $n = random()->randomNumber()])
            ->with(['bar' => $bar = BarBuilder::new()])
            ->build();

        assertEquals(
            new Foo($text, $n, $bar->build()),
            $fooBuiltWithArray,
        );
    }

    public function test_it_is_buildatable_with_arg_unpacking(): void
    {
        $fooBuiltWithArgsUnpacking = FooBuilderWithArgUnpacking::new()
            ->named($text = random()->name())
            ->with(number: $n = random()->randomNumber())
            ->with(bar: $bar = BarBuilder::new())
            ->build();

        assertEquals(
            new Foo($text, $n, $bar->build()),
            $fooBuiltWithArgsUnpacking,
        );
    }
}

final class Foo
{
    public function __construct(
        public string $name,
        public int $number,
        public Bar $bar,
    ) {}
}

final class Bar
{
    public function __construct(
        public string $value,
    ) {}
}

/**
 * @implements Buildatable<Foo>
 */
final class FooBuilderWithArray implements Buildatable
{
    use BuildableWithArray;

    public function __construct(
        public string $name,
        public int $number,
        public Bar $bar,
    ) {}

    public static function new(): static
    {
        $random = random();

        return new static(
            $random->name(),
            $random->randomNumber(),
            BarBuilder::new()->build(),
        );
    }

    public function build(): Foo
    {
        return new Foo($this->name, $this->number, $this->bar);
    }

    public function named(string $name): static
    {
        return $this->with(['name' => $name]);
    }
}

/**
 * @implements Buildatable<Foo>
 */
final class FooBuilderWithArgUnpacking implements Buildatable
{
    use BuildableWithArgUnpacking;

    public function __construct(
        public string $name,
        public int $number,
        public Bar $bar,
    ) {}

    public static function new(): static
    {
        $random = random();

        return new static(
            $random->name(),
            $random->randomNumber(),
            BarBuilder::new()->build(),
        );
    }

    public function build(): Foo
    {
        return new Foo($this->name, $this->number, $this->bar);
    }

    public function named(string $name): static
    {
        return $this->with(name: $name);
    }
}

/**
 * @implements Buildatable<Bar>
 */
final class BarBuilder implements Buildatable
{
    use BuildableWithArgUnpacking;

    public function __construct(
        public string $value,
    ) {}

    public static function new(): static
    {
        $random = random();

        return new static($random->text());
    }

    public function build(): Bar
    {
        return new Bar($this->value);
    }
}
