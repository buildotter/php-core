<?php declare(strict_types=1);

namespace Buildotter\Core;

use InvalidArgumentException;
use ReflectionProperty;

trait BuildableValidateParam
{
    /**
     * @param array<int|string, mixed> $arguments
     * @param array<ReflectionProperty> $properties
     * @throws InvalidArgumentException
     * @return void
     */
    public function validateArguments(array $arguments, array $properties): void
    {
        $propertiesName = [];
        \array_walk(
            array: $properties,
            callback: function (\ReflectionProperty $property) use (&$propertiesName) {
                $propertiesName[$property->getName()] = $property->getName();
            },
        );
        $invalidArgs = \array_diff_key($arguments, $propertiesName);
        if (0 !== \count($invalidArgs)) {
            throw new InvalidArgumentException(
                message: \sprintf(
                    'Arguments %s dont match with call function',
                    \implode(separator: ',', array: $invalidArgs),
                ),
            );
        }
    }
}
