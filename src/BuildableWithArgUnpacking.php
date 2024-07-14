<?php

declare(strict_types=1);

namespace Buildotter\Core;

trait BuildableWithArgUnpacking
{
    /**
     * @param mixed ...$values
     */
    public function with(...$values): static
    {
        $r = new \ReflectionClass(static::class);

        $clone = $r->newInstanceWithoutConstructor();

        foreach ($r->getProperties() as $property) {
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
