<?php

declare(strict_types=1);

namespace Buildotter\Core;

trait BuildableWithArray
{
    use BuildableValidateParam;
    /**
     * @param array<string, mixed> $values
     */
    public function with(array $values): static
    {
        $r = new \ReflectionClass(static::class);

        $clone = $r->newInstanceWithoutConstructor();

        $this->validateArguments(
            arguments: $values,
            properties: $r->getProperties(),
        );

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
