<?php

declare(strict_types=1);

namespace Buildotter\Tests\Core;

use Buildotter\Core\BuildableWithArgUnpacking;
use Buildotter\Core\BuildableWithArray;
use Buildotter\Core\Buildatable;
use Buildotter\Core\Exception\UnknownPropertyException;
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

    public function test_it_is_not_buildatable_with_bad_arg_unpacking(): void
    {
        $this->expectException(UnknownPropertyException::class);
        $this->expectExceptionMessage('The following properties do not exist in "Buildotter\Tests\Core\FooBuilderWithArgUnpacking": "0", "doesNotExist", "doesNotExistEither".');

        FooBuilderWithArgUnpacking::new()
            ->with(
                random()->word(),
                doesNotExist: random()->word(),
                number: random()->randomNumber(),
                doesNotExistEither: random()->word(),
            )
            ->build();
    }

    public function test_it_is_not_buildatable_with_bad_arg_unpacking_2(): void
    {
        $this->expectException(UnknownPropertyException::class);
        $this->expectExceptionMessage('The following properties do not exist in "Buildotter\Tests\Core\FooBuilderWithArgUnpacking": "doesNotExist".');

        FooBuilderWithArgUnpacking::new()
            ->named(random()->name())
            ->with(doesNotExist: random()->word())
            ->with(doesNotExistEither: random()->word())
            ->build();
    }

    public function test_it_is_not_buildatable_with_bad_arg_array(): void
    {
        $this->expectException(UnknownPropertyException::class);
        $this->expectExceptionMessage('The following properties do not exist in "Buildotter\Tests\Core\FooBuilderWithArray": "doesNotExist", "doesNotExistEither".');

        FooBuilderWithArray::new()
            ->with([
                'doesNotExist' => random()->word(),
                'number' => random()->randomNumber(),
                'doesNotExistEither' => random()->word(),
            ])
            ->build();
    }

    public function test_it_is_not_buildatable_with_bad_arg_array_2(): void
    {
        $this->expectException(UnknownPropertyException::class);
        $this->expectExceptionMessage('The following properties do not exist in "Buildotter\Tests\Core\FooBuilderWithArray": "doesNotExist".');

        FooBuilderWithArray::new()
            ->named(random()->name())
            ->with(['doesNotExist' => random()->word()])
            ->with(['doesNotExistEither' => random()->word()])
            ->build();
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
