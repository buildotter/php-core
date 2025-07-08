<?php

declare(strict_types=1);

namespace Buildotter\Core\Exception;

final class UnknownPropertyException extends \InvalidArgumentException
{
    /**
     * @param array<string|int> $properties
     */
    public static function new(array $properties, string $class): self
    {
        return new self(
            \sprintf(
                'The following properties do not exist in "%s": "%s".',
                $class,
                \implode('", "', $properties),
            ),
        );
    }
}
