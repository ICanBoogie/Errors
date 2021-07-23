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

use Throwable;

/**
 * An error collection.
 */
class ErrorCollection implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable, ToArray
{
    /**
     * Special identifier used when an error is not associated with a specific attribute.
     */
    public const GENERIC = '__generic__';

    /**
     * @var Error[][]
     */
    private $collection = [];

    /**
     * Add an error associated with an attribute.
     *
     * @param string $attribute Attribute name.
     * @param Error|string|bool $error_or_format_or_true A {@link Error} instance or
     * a format to create that instance, or `true`.
     * @param array $args Only used if `$error_or_format_or_true` is not a {@link Error}
     * instance or `true`.
     *
     * @return $this
     */
    public function add($attribute, $error_or_format_or_true = true, array $args = [])
    {
        $this->assert_valid_attribute($attribute);
        $this->assert_valid_error($error_or_format_or_true);

        $this->collection[$attribute][] = $this
            ->ensure_error_instance($error_or_format_or_true, $args);

        return $this;
    }

    /**
     * Add an error not associated with any attribute.
     *
     * @param Error|string|bool $error_or_format_or_true A {@link Error} instance or
     * a format to create that instance, or `true`.
     * @param array $args Only used if `$error_or_format_or_true` is not a {@link Error}
     * instance or `true`.
     *
     * @return $this
     */
    public function add_generic($error_or_format_or_true = true, array $args = [])
    {
        return $this->add(self::GENERIC, $error_or_format_or_true, $args);
    }

    /**
     * Asserts that an attribute is valid.
     *
     * @param string $attribute
     */
    protected function assert_valid_attribute($attribute)
    {
        if (is_string($attribute)) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            "\$attribute must a string. Given: `%s`",
            Error::class,
            is_object($attribute)
                ? get_class($attribute)
                : gettype($attribute)
        ));
    }

    /**
     * Asserts that the error type is valid.
     *
     * @param mixed $error_or_format_or_true
     */
    protected function assert_valid_error($error_or_format_or_true)
    {
        if (
            $error_or_format_or_true === true
            || is_string($error_or_format_or_true)
            || $error_or_format_or_true instanceof Error
            || $error_or_format_or_true instanceof Throwable
        ) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            "\$error_or_format_or_true must be a an instance of `%s`, a string, or true. Given: `%s`",
            Error::class,
            is_object($error_or_format_or_true)
                ? get_class($error_or_format_or_true)
                : gettype($error_or_format_or_true)
        ));
    }

    /**
     * Ensures a {@link Error} instance.
     *
     * @param Error|string|bool $error_or_format_or_true
     * @param array $args
     *
     * @return Error
     */
    protected function ensure_error_instance($error_or_format_or_true, array $args = [])
    {
        $error = $error_or_format_or_true;

        if (!$error instanceof Error) {
            $error = new Error($error === true ? "" : (string) $error, $args);
        }

        return $error;
    }

    /**
     * Adds an error.
     *
     * @param string $attribute
     * @param Error|string|true $error
     *
     * @see add()
     */
    public function offsetSet($attribute, $error)
    {
        $this->add($attribute === null ? self::GENERIC : $attribute, $error);
    }

    /**
     * Clears the errors of an attribute.
     *
     * @param string|null $attribute Attribute name or `null` for _generic_.
     */
    public function offsetUnset($attribute)
    {
        unset($this->collection[$attribute === null ? self::GENERIC : $attribute]);
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
     * @param string|null $attribute Attribute name or `null` for _generic_.
     *
     * @return boolean true if an error is defined for the specified attribute, false otherwise.
     */
    public function offsetExists($attribute)
    {
        return isset($this->collection[$attribute === null ? self::GENERIC : $attribute]);
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
     * @param string|null $attribute Attribute name or `null` for _generic_.
     *
     * @return Error[]
     */
    public function offsetGet($attribute)
    {
        if (!$this->offsetExists($attribute)) {
            return [];
        }

        return $this->collection[$attribute === null ? self::GENERIC : $attribute];
    }

    /**
     * Clears errors.
     *
     * @return $this
     */
    public function clear()
    {
        $this->collection = [];

        return $this;
    }

    /**
     * Merges with another error collection.
     *
     * @param ErrorCollection $collection
     */
    public function merge(ErrorCollection $collection)
    {
        foreach ($collection as $attribute => $error) {
            $this->add($attribute, $error);
        }
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
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
     *     echo "$attribute => $error<br />";
     *
     * });
     * </pre>
     *
     * @param callable $callback Function to execute for each element, taking three arguments:
     *
     * - `Error $error`: The current error.
     * - `string $attribute`: The attribute or {@link self::GENERIC}.
     * - `ErrorCollection $collection`: This instance.
     */
    public function each(callable $callback)
    {
        foreach ($this as $attribute => $error) {
            $callback($error, $attribute, $this);
        }
    }

    /**
     * Returns the total number of errors.
     *
     * @inheritdoc
     */
    public function count()
    {
        return count($this->collection, COUNT_RECURSIVE) - count($this->collection);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->to_array();
    }

    /**
     * Converts the object into an array.
     *
     * @return Error[][]
     */
    public function to_array(): array
    {
        return array_filter(array_merge([ self::GENERIC => [] ], $this->collection));
    }
}
