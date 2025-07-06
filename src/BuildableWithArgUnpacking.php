<?php

declare(strict_types=1);

namespace Buildotter\Core;

use Buildotter\Core\Exception\UnknownPropertyException;

trait BuildableWithArgUnpacking
{
    /**
     * @param mixed ...$values
     */
    public function with(...$values): static
    {
        $r = new \ReflectionClass(static::class);
        $properties = $r->getProperties();

        $propertyNames = \array_map(static fn($p) => $p->getName(), $properties);
        $invalidArguments = \array_diff(\array_keys($values), $propertyNames);
        if ([] !== $invalidArguments) {
            throw UnknownPropertyException::new($invalidArguments, static::class);
        }

        $clone = $r->newInstanceWithoutConstructor();

        foreach ($properties as $property) {
            $field = $property->name;
            $clone->$field = match (\array_key_exists($field, $values)) {
                true => (static function () use ($values, $field) {
                    $value = $values[$field];

                    if ($value instanceof Buildatable) {
                        return $value->build();
                    }

                    return $value;
                })(),
                default => $property->getValue($this),
            };
        }

        return $clone;
    }
}
