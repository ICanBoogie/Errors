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
 * Representation of an error.
 *
 * @property-read string $format
 * @property-read array $args
 */
class Error implements \JsonSerializable
{
	/**
	 * @var string
	 */
	private $format;

	/**
	 * @var array
	 */
	private $args;

	/**
	 * @param string $format
	 * @param array $args
	 */
	public function __construct($format, array $args = [])
	{
		$this->format = $format;
		$this->args = $args;
	}

	/**
	 * @inheritdoc
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'format':
				return $this->format;
			case 'args':
				return $this->args;
			default:
				throw new \LogicException("Undefined property: $name");
		}
	}

	/**
	 * @inheritdoc
	 */
	public function __toString()
	{
		return format($this->format, $this->args);
	}

	/**
	 * @inheritdoc
	 */
	function jsonSerialize()
	{
		return (string) $this;
	}
}
