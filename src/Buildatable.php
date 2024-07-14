<?php

declare(strict_types=1);

namespace Buildotter\Core;

/**
 * @template T
 */
interface Buildatable
{
    public static function random(): static;

    /**
     * @return T
     */
    public function build();
}
