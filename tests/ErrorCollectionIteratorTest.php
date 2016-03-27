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
 * @group render
 */
class ErrorCollectionIteratorTest extends \PHPUnit_Framework_TestCase
{
	public function test_renderer()
	{
		$format = "error: {arg}";
		$arg1 = uniqid();
		$arg2 = uniqid();
		$arg3 = uniqid();
		$arg4 = uniqid();
		$attribute = uniqid();

		$errors = (new ErrorCollection)
			->add($attribute, $format, [ 'arg' => $arg3 ])
			->add_generic($format, [ 'arg' => $arg1 ])
			->add($attribute, $format, [ 'arg' => $arg4 ])
			->add_generic($format, [ 'arg' => $arg2 ])
		;

		$renderer = new ErrorCollectionIterator($errors);
		$rendered = [];

		foreach ($renderer as $a => $r)
		{
			$rendered[] = [ $a, $r ];
		}

		$this->assertSame([

			[ ErrorCollection::GENERIC, "error: {$arg1}" ],
			[ ErrorCollection::GENERIC, "error: {$arg2}" ],
			[ $attribute, "error: {$arg3}" ],
			[ $attribute, "error: {$arg4}" ],

		], $rendered);
	}

	public function test_render_with_customer_error_renderer()
	{
		$format = "error: {arg}";
		$arg1 = uniqid();
		$arg2 = uniqid();
		$arg3 = uniqid();
		$arg4 = uniqid();
		$attribute = uniqid();

		$errors = (new ErrorCollection)
			->add($attribute, $format, [ 'arg' => $arg3 ])
			->add_generic($format, [ 'arg' => $arg1 ])
			->add($attribute, $format, [ 'arg' => $arg4 ])
			->add_generic($format, [ 'arg' => $arg2 ])
		;

		$renderer = new ErrorCollectionIterator($errors, function (Error $error, $attribute, ErrorCollection $collection) use ($errors) {

			$this->assertSame($errors, $collection);

			return strrev($error);

		});

		$rendered = [];

		foreach ($renderer as $a => $r)
		{
			$rendered[] = [ $a, $r ];
		}

		$this->assertSame([

			[ ErrorCollection::GENERIC, strrev("error: {$arg1}") ],
			[ ErrorCollection::GENERIC, strrev("error: {$arg2}") ],
			[ $attribute, strrev("error: {$arg3}") ],
			[ $attribute, strrev("error: {$arg4}") ],

		], $rendered);
	}
}
