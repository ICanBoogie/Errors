<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

use JsonSerializable;

/**
 * Representation of an error.
 */
class Error implements JsonSerializable
{
    /**
     * @param array<int|string, mixed> $args
     */
    public function __construct(
        public readonly string $format,
        public readonly array $args = []
    ) {
    }

    public function __toString()
    {
        return format($this->format, $this->args);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): string
    {
        return (string) $this;
    }
}
