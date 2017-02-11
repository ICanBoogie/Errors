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
 * @group unit
 */
class ErrorTest extends \PHPUnit_Framework_TestCase
{
	public function test_getters()
	{
		$format = uniqid();
		$args = [ uniqid() => uniqid() ];
		$error = new Error($format, $args);

		$this->assertSame($format, $error->format);
		$this->assertSame($args, $error->args);
	}

	/**
	 * @expectedException \LogicException
	 */
	public function test_should_throw_exception_on_getting_undefined_property()
	{
		$error = new Error("");
		$error->{ uniqid() };
	}
}
