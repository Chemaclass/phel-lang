<?php

declare(strict_types=1);

namespace Phel\Lang;

interface IIdentical
{
    /**
     * Checks if $other is identical to $this.
     *
     * @param mixed $other The value to compare
     */
    public function identical($other): bool;
}
