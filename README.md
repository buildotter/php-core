<div align="center">
  <img alt="Buildotter Logo" src="https://avatars.githubusercontent.com/u/175545150?v=4&s=400" height="240"/>
  <h1>Buildotter Core</h1>
</div>

[![Tests](https://github.com/buildotter/php-core/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/buildotter/php-core/actions?workflow=tests)
[![License](https://img.shields.io/github/license/buildotter/php-core)](/LICENSE)
[![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/buildotter/php-core)](https://github.com/buildotter/php-core/releases/latest)
[![Conventional Commits](https://img.shields.io/badge/Conventional%20Commits-1.0.0-blue.svg?style=flat)](https://conventionalcommits.org)

Foundation to implement the **Test Data Builder Pattern** in [PHP](https://www.php.net/).

## Installation

```bash
composer require --dev buildotter/php-core
```

## What's the Test Data Builder Pattern?

Creating and managing test data is a critical aspect of ensuring the quality and reliability of an application.

The Test Data Builder Pattern is a technique for simplifying the process of constructing test data,
making tests more maintainable and readable.

It provides a structured way to create complex and consistent test data for your test cases.

This pattern isolate the process of creating test data from the actual test logic.

By doing so, it promotes separation of concerns and helps you write cleaner and more focused tests.

It helps for the following problems:

* How to decouple object creation from implementation in your tests?
* How to improve code readability in your tests?
* How to speed up writing your test setup?

The 4 rules to keep in mind are:

* Have a property for each constructor parameter
* Initialize its properties to commonly used or safe values
* Have a build method that creates a new object using the values in its properties
* Have chainable public methods for overriding the values in its properties

To know more about the Test Data Builder Pattern, you can read the following articles:
* [Test Data Builders: an alternative to the Object Mother pattern](https://web.archive.org/web/20231224102029/https://www.natpryce.com/articles/000714.html): we encourage you to read the whole series
* [Xtrem T.D.D's explanation of Test Data Builders](https://xtrem-tdd.netlify.app/Flavours/Testing/test-data-builders)
* [Are You Using Mocks When You Should Be Using Test Data Builders?](https://web.archive.org/web/20210413233340/http://www.valuablecode.com/2009/03/are-you-using-mocks-when-you-should-be-using-test-data-builders/)

## Why do we need libraries to implement the Test Data Builder Pattern?

We don't.

It is more about helping with repetitive tasks, speed up the process of creating test data builders and being focused on the test logic itself.

With time passing, we gathered parts that are common to projects. So why not share them?

Furthermore, it helps to have a common foundation for the adoption of the pattern in a team.

## Usage

We tried not to be too opinionated about the foundation here.
For instance, we like to use functions like `anElephant()` instead of `ElephantBuilder::new()`
(we find it more readable and allow us to focus on what is important) but nothing
forces you to do the same.

### The main foundation: `Buildotter\Core\Buildatable` interface

You may found the name weird, it is a mix of "build" and "data" with the suffix "-able".
"-able" is used to form adjectives from verbs, with the meaning "capable of, fit for, tending to".
With this name, we want to express that the object is capable of building data as a test data builder.

The `Buildotter\Core\Buildatable::new()` method is the named constructor to create a new instance of the class with commonly used or safe values.
Imagine that you develop a dating app, you know the age of your customers.
Most of the time you don't mind about the exact age, you just need one respecting the invariants of your domain
(see [Propery-Based Testing](https://beram-presentation.gitlab.io/property-based-testing-101) for more about this).
It is less likely that a customer is 300 or 10 years old than between 18 and 60 years old for instance.
It means that a commonly used or safe value for the age of a customer is between 18 and 60 years old.
So instead of choosing an arbitrary value, you may use a random value between 18 and 60 years old.

The `Buildotter\Core\Buildatable::build()` method to create a new object using the values in its properties.

### A `::with()` method with `Buildotter\Core\BuildableWithXXX` traits

We provide some traits that already implement a `::with()` method.

`Buildotter\Core\BuildableWithArgUnpacking` allows you to use named arguments to set the properties of the builder.

```php
anElephant()->with(
    name: 'Elmer',
    birth: new \DateTimeImmutable(),
);
```

`Buildotter\Core\BuildableWithArray` allows you to set properties of the builder by passing an array as its argument.

```php
anElephant()->with([
    'name' => 'Elmer',
    'birth' => new \DateTimeImmutable(),
]);
```

You may prefer one or the other depending on your preferences.
You may choose to use both.
You may choose to not use them at all.

### Build multiple objects

You may need multiple objects of the same type at once.
`Buildotter\Core\Many` static methods are here to help you.

## Example

Imagine you have a class `Elephant` with the following constructor:

```php
namespace App\Entity;

final class Elephant
{
    public function __construct(
        private Uuid $id,
        private string $name,
        private \DateTimeImmutable $birth,
        /** @var Topic[] */
        private array $topics = [],
    ) {}
}
```

You may create a test data builder for this class like this:

```php
namespace App\Fixtures\Builder;

use App\Entity\Elephant;
use App\Entity\Topic;
use Buildotter\Core\Buildatable;
use Buildotter\Core\BuildableWithArgUnpacking;

/**
 * @implements Buildatable<Elephant>
 */
final readonly class ElephantBuilder implements Buildatable
{
    use BuildableWithArgUnpacking;

    public function __construct(
        private Uuid $id,
        private string $name,
        private \DateTimeImmutable $birth,
        /** @var Topic[] */
        private array $topics,
    ) {}

    public static function new(): self
    {
        $random = \random();

        // Note that we try to use commonly used or safe values.
        return new self(
            new Uuid(),
            $random->name(),
            \DateTimeImmutable::createFromMutable($random->dateTimeBetween('-50 years', 'now')),
            // You may see here that it may be necessary to build multiple objects sometimes.
            \someTopics($random->numberBetween(0, 10)),
        );
    }

    public function build(): Elephant
    {
        return new Elephant(
            $this->id,
            $this->name,
            $this->birth,
            $this->topics
        );
    }

    // Providing domain specific methods instead of only relying on the `with()` method
    // allows you to focus on what is important in your tests.
    // It helps to make your tests more readable and maintainable.
    //
    // Note here that we did not find the need to implement a domain specific method
    // to change the `$topics` property.
    // We may rely on the `with()` method as long as we don't find useful to have
    // a method like `interestedInTopics(array $topics)` or whatever.

    public function named(string $name): self
    {
        return $this->with(name: $name);
    }

    public function bornThe(\DateTimeImmutable|string $birth): self
    {
        return $this->with(birth: $birth instanceof \DateTimeImmutable ? $birth : \DateTimeImmutable::createFromFormat('Y-m-d', $birth));
    }

    public function yearsOld(int $age): self
    {
        if ($age <= 0) {
            throw new \InvalidArgumentException('Age must be positive');
        }

        return $this->with(birth: new \DateTimeImmutable(\sprintf('-%d years', $age)));
    }

    public function minor(): self
    {
        return $this->yearsOld(\random()->numberBetween(1, 17));
    }
}
```

Feel free to create functions like `anElephant()` and `someTopics()` to make your tests more readable.

For instance, we may have a file containing the following functions:

```php

use App\Fixtures\Builder\ElephantBuilder;
use App\Fixtures\Builder\TopicBuilder;

function random(): \Faker\Generator
{
    return \Faker\Factory::create();
}

function anElephant(): ElephantBuilder
{
    return ElephantBuilder::new();
}

/**
 * @return App\Entity\Elephant[]
 */
function someElephants(int|null $numberOfItems = null): array
{
    return Many::from(ElephantBuilder::class, $numberOfItems);
}

function aTopic(): TopicBuilder
{
    return TopicBuilder::new();
}

/**
 * @return App\Entity\Topic[]
 */
function someTopics(int|null $numberOfItems = null): array
{
    return Many::from(TopicBuilder::class, $numberOfItems);
}
```

And then, during testing:

```php
use PHPUnit\Framework\TestCase;

final class RentBookTest extends TestCase
{
    public function test_a_minor_cannot_rent_a_book_for_adult(): void
    {
        $minor = \anElephant()->minor()->build();

        expectException(UnderAgeException::class);

        (new RentBook())($minor, \aBook()->forAdult()->build());
    }
}
```

## More

To generate test data builders, you may use the [Buildotter Maker Standalone](https://github.com/buildotter/php-maker-standalone).
Instead, you may prefer to use [PHPStorm Live templates](https://www.jetbrains.com/help/phpstorm/using-live-templates.html).
Or combine them.

> Question: Do you think that it could be helpful to share some live templates that helps for testing?

## Contributing

See the [contributing guide](CONTRIBUTING.md).

## TODOs

- [ ] provide a symfony bridge in a dedicated package
