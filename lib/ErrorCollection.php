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

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Throwable;
use Traversable;

use function get_debug_type;

/**
 * An error collection.
 *
 * @implements ArrayAccess<string, Error[]>
 * @implements IteratorAggregate<string, Error>
 */
class ErrorCollection implements ArrayAccess, IteratorAggregate, Countable, JsonSerializable, ToArray
{
    /**
     * Special identifier used when an error is not associated with a specific attribute.
     */
    public const GENERIC = '__generic__';

    /**
     * @var array<string, Error[]>
     */
    private array $collection = [];

    /**
     * Add an error associated with an attribute.
     *
     * @param string $attribute Attribute name.
     * @param Throwable|bool|string|Error $error_or_format_or_true A {@link Error} instance or
     * a format to create that instance, or `true`.
     * @param array<int|string, mixed> $args Only used if `$error_or_format_or_true` is not a {@link Error}
     * instance or `true`.
     *
     * @return $this
     */
    public function add(
        string $attribute,
        Throwable|bool|string|Error $error_or_format_or_true = true,
        array $args = []
    ): static {
        $this->assert_valid_error($error_or_format_or_true);

        $this->collection[$attribute][] = $this
            ->ensure_error_instance($error_or_format_or_true, $args);

        return $this;
    }

    /**
     * Add an error not associated with any attribute.
     *
     * @param Throwable|bool|string|Error $error_or_format_or_true A {@link Error} instance or
     * a format to create that instance, or `true`.
     * @param array<int|string, mixed> $args Only used if `$error_or_format_or_true` is not a {@link Error}
     * instance or `true`.
     *
     * @return $this
     */
    public function add_generic(
        Throwable|bool|string|Error $error_or_format_or_true = true,
        array $args = []
    ): static {
        return $this->add(self::GENERIC, $error_or_format_or_true, $args);
    }

    /**
     * Asserts that the error type is valid.
     *
     * @param mixed $error_or_format_or_true
     */
    private function assert_valid_error(mixed $error_or_format_or_true): void
    {
        if (
            $error_or_format_or_true === true
            || is_string($error_or_format_or_true)
            || $error_or_format_or_true instanceof Error
            || $error_or_format_or_true instanceof Throwable
        ) {
            return;
        }

        throw new InvalidArgumentException(sprintf(
            "\$error_or_format_or_true must be a an instance of `%s`, a string, or true. Given: `%s`",
            Error::class,
            get_debug_type($error_or_format_or_true)
        ));
    }

    /**
     * Ensures a {@link Error} instance.
     *
     * @param Throwable|bool|string|Error $error_or_format_or_true
     * @param array<int|string, mixed> $args
     */
    private function ensure_error_instance(
        Throwable|bool|string|Error $error_or_format_or_true,
        array $args = []
    ): Error {
        $error = $error_or_format_or_true;

        if (!$error instanceof Error) {
            $error = new Error($error === true ? "" : (string) $error, $args);
        }

        return $error;
    }

    /**
     * Adds an error.
     *
     * @param string|null $offset An attribute name or `null` for _generic_.
     * @param Throwable|Error|string|bool $value An error.
     *
     * @see add()
     *
     * @phpstan-ignore-next-line
     */
    public function offsetSet($offset, $value): void
    {
        $this->add($offset ?? self::GENERIC, $value);
    }

    /**
     * Clears the errors of an attribute.
     *
     * @param string|null $offset An attribute name or `null` for _generic_.
     */
    public function offsetUnset($offset): void
    {
        unset($this->collection[$offset ?? self::GENERIC]);
    }

    /**
     * Checks if an error is defined for an attribute.
     *
     * ```php
     * <?php
     *
     * use ICanBoogie\ErrorCollection
     *
     * $errors = new ErrorCollection;
     * isset($errors['username']);
     * // false
     * $errors->add('username');
     * isset($errors['username']);
     * // true
     * ```
     *
     * @param string|null $offset An attribute name or `null` for _generic_.
     */
    public function offsetExists($offset): bool
    {
        return isset($this->collection[$offset ?? self::GENERIC]);
    }

    /**
     * Returns errors associated with an attribute.
     *
     * ```php
     * <?php
     *
     * use ICanBoogie\ErrorCollection;
     *
     * $errors = new ErrorCollection;
     * $errors['password']
     * // []
     * $errors->add('password')
     * // [ Message ]
     * ```
     *
     * @param string|null $offset An attribute name or `null` for _generic_.
     *
     * @return Error[]
     */
    public function offsetGet($offset): array
    {
        if (!$this->offsetExists($offset)) {
            return [];
        }

        return $this->collection[$offset ?? self::GENERIC];
    }

    /**
     * Clears errors.
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->collection = [];

        return $this;
    }

    /**
     * Merges with another error collection.
     */
    public function merge(ErrorCollection $collection): void
    {
        foreach ($collection as $attribute => $error) {
            $this->add($attribute, $error);
        }
    }

    /**
     * @return Traversable<string, Error>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->to_array() as $attribute => $errors) {
            foreach ($errors as $error) {
                yield $attribute => $error;
            }
        }
    }

    /**
     * Iterates through errors using a callback.
     *
     * ```php
     * <?php
     *
     * use ICanBoogie\ErrorCollection;
     *
     * $errors = new ErrorCollection;
     * $errors->add('username', "Funny user name");
     * $errors->add('password', "Weak password");
     *
     * $errors->each(function ($error, $attribute, $errors) {
     *
     *     echo "$attribute => $error\n";
     *
     * });
     * ```
     *
     * @param callable(Error, string $attribute, ErrorCollection): void $callback
     *     Function to execute for each element, taking three arguments:
     *
     *     - `Error $error`: The current error.
     *     - `string $attribute`: The attribute or {@link self::GENERIC}.
     *     - `ErrorCollection $collection`: This instance.
     */
    public function each(callable $callback): void
    {
        foreach ($this as $attribute => $error) {
            $callback($error, $attribute, $this);
        }
    }

    /**
     * Returns the total number of errors.
     *
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->collection, COUNT_RECURSIVE) - count($this->collection);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->to_array();
    }

    /**
     * Converts the object into an array.
     *
     * @return array<string, Error[]>
     */
    public function to_array(): array
    {
        return array_filter(array_merge([ self::GENERIC => [] ], $this->collection));
    }
}
