<?php

declare(strict_types=1);

namespace Buildotter\Core;

/**
 * @template T
 */
interface Buildatable
{
    /**
     * The named constructor to create a new instance of the class with commonly used or safe values.
     *
     * Imagine that you develop a dating app, you know the age of your customers.
     * Most of the time you don't mind about the exact age, you just need one respecting the invariants of your domain
     * (see [Propery-Based Testing](https://beram-presentation.gitlab.io/property-based-testing-101) for more about this).
     * It is less likely that a customer is 300 or 10 years old than between 18 and 60 years old for instance.
     * It means that a commonly used or safe value for the age of a customer is between 18 and 60 years old.
     * So instead of choosing an arbitrary value, you may use a random value between 18 and 60 years old.
     */
    public static function new(): static;

    /**
     * Create a new object using the values in its properties.
     *
     * @return T
     */
    public function build();
}
