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

/**
 * Representation of a rendered error collection.
 *
 * **Note:** The error collection is actually rendered as the instance is iterated.
 */
class RenderedErrorCollection implements \IteratorAggregate
{
	/**
	 * @var ErrorCollection
	 */
	private $collection;

	/**
	 * @var callable|RenderError
	 */
	private $render_error;

	/**
	 * @param ErrorCollection $collection
	 * @param RenderError|callable $render_error
	 */
	public function __construct(ErrorCollection $collection, callable $render_error = null)
	{
		$this->collection = $collection;
		$this->render_error = $render_error;
	}

	/**
	 * @inheritdoc
	 */
	public function getIterator()
	{
		/* @var $error Error */

		foreach ($this->collection as $attribute => $error)
		{
			yield $attribute => $this->render_error($error, $attribute);
		}
	}

	/**
	 * Renders an error into a string.
	 *
	 * @param Error $error
	 * @param string $attribute
	 *
	 * @return string
	 */
	protected function render_error(Error $error, $attribute)
	{
		$render_error = $this->render_error;

		return $render_error ? $render_error($error, $attribute, $this->collection) : (string) $error;
	}
}
