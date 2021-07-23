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
use LogicException;

/**
 * Representation of an error.
 *
 * @property-read string $format
 * @property-read array $args
 */
class Error implements JsonSerializable
{
    /**
     * @param array<int|string, mixed> $args
     */
    public function __construct(
        private string $format,
        private array $args = []
    ) {
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'format' => $this->format,
            'args' => $this->args,
            default => throw new LogicException("Undefined property: $name"),
        };
    }

    public function __toString()
    {
        return format($this->format, $this->args);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return (string) $this;
    }
}
