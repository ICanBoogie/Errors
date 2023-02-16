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

use IteratorAggregate;
use Traversable;

/**
 * Iterates over an error collection and return rendered errors.
 *
 * @implements IteratorAggregate<string, string>
 */
class ErrorCollectionIterator implements IteratorAggregate
{
    /**
     * @var ErrorRenderer|callable
     * @phpstan-var ErrorRenderer|(callable(Error,string $attribute,ErrorCollection):string)
     */
    private $render_error;

    /**
     * @phpstan-param ErrorRenderer|(callable(Error,string $attribute,ErrorCollection):string)|null $render_error
     */
    public function __construct(
        private readonly ErrorCollection $collection,
        ErrorRenderer|callable $render_error = null
    ) {
        $this->render_error = $render_error ?? fn(Error $error) => (string) $error;
    }

    /**
     * @return Traversable<string, string>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->collection as $attribute => $error) {
            yield $attribute => $this->render_error($error, $attribute);
        }
    }

    /**
     * Renders an error into a string.
     */
    private function render_error(Error $error, string $attribute): string
    {
        return ($this->render_error)($error, $attribute, $this->collection);
    }
}
