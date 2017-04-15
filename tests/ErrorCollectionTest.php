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
 * @group error
 */
class ErrorCollectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ErrorCollection
	 */
	private $errors;

	public function setUp()
	{
		$this->errors = new ErrorCollection;
	}

	public function test_add_with_string()
	{
		$attribute = uniqid();
		$format = uniqid();
		$args = [ uniqid() => uniqid() ];
		$this->errors->add($attribute, $format, $args);
		$this->assertEquals(1, $this->errors->count());

		$errors = $this->errors[$attribute];
		$this->assertInternalType('array', $errors);
		$error = reset($errors);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertSame($format, $error->format);
		$this->assertSame($args, $error->args);
	}

	public function test_add_with_true()
	{
		$attribute = uniqid();
		$args = [ uniqid() => uniqid() ];
		$this->errors->add($attribute, true, $args);
		$this->assertEquals(1, $this->errors->count());

		$errors = $this->errors[$attribute];
		$this->assertInternalType('array', $errors);
		$error = reset($errors);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertSame("", $error->format);
		$this->assertSame($args, $error->args);
	}

	public function test_add_with_error()
	{
		$error = new Error(uniqid(), [ uniqid() => uniqid() ]);
		$attribute = uniqid();
		$this->errors->add($attribute, $error, [ uniqid() => uniqid() ]);
		$this->assertEquals(1, $this->errors->count());

		$errors = $this->errors[$attribute];
		$this->assertInternalType('array', $errors);
		$this->assertSame($error, reset($errors));
	}

	public function test_add_with_exception()
	{
		$error = new \Exception;
		$attribute = uniqid();
		$this->errors->add($attribute, $error, [ uniqid() => uniqid() ]);
		$this->assertEquals(1, $this->errors->count());

		$errors = $this->errors[$attribute];
		$this->assertInternalType('array', $errors);
		$this->assertSame((string) $error, reset($errors)->format);
	}

	/**
	 * @requires PHP 7.0
	 */
	public function test_add_with_throwable()
	{
		$error = new \Error;
		$attribute = uniqid();
		$this->errors->add($attribute, $error, [ uniqid() => uniqid() ]);
		$this->assertEquals(1, $this->errors->count());

		$errors = $this->errors[$attribute];
		$this->assertInternalType('array', $errors);
		$this->assertSame((string) $error, reset($errors)->format);
	}

	/**
	 * @dataProvider provide_test_add_with_invalid_attribute
	 * @expectedException \InvalidArgumentException
	 *
	 * @param mixed $attribute
	 */
	public function test_add_with_invalid_attribute($attribute)
	{
		$this->errors->add($attribute, "Error");
	}

	public function provide_test_add_with_invalid_attribute()
	{
		return [

			[ null ],
			[ false ],
			[ true ],
			[ 123 ],
			[ [] ],
			[ new \stdClass ]

		];
	}

	/**
	 * @dataProvider provide_test_add_with_invalid_type
	 * @expectedException \InvalidArgumentException
	 *
	 * @param mixed $error
	 */
	public function test_add_with_invalid_type($error)
	{
		$this->errors->add(uniqid(), $error);
	}

	public function provide_test_add_with_invalid_type()
	{
		return [

			[ false ],
			[ [ uniqid() => uniqid() ] ],
			[ 1234 ],
			[ new \stdClass ]

		];
	}

	public function test_add_generic()
	{
		$error = new Error(uniqid(), [ uniqid() => uniqid() ]);
		$this->errors->add_generic($error);
		$errors = $this->errors[null];
		$this->assertSame($error, reset($errors));
	}

	public function test_array_access_interface()
	{
		$errors = $this->errors;
		$err1 = new Error(uniqid(), [ uniqid() => uniqid() ]);
		$err2 = new Error(uniqid(), [ uniqid() => uniqid() ]);
		$err3 = new Error(uniqid(), [ uniqid() => uniqid() ]);
		$err4 = new Error(uniqid(), [ uniqid() => uniqid() ]);
		$attribute = uniqid();
		$errors[] = $err1;
		$errors[] = $err2;
		$errors[$attribute] = $err3;
		$errors[$attribute] = $err4;

		$this->assertTrue(isset($errors[null]));
		$this->assertTrue(isset($errors[$attribute]));
		$this->assertSame([ $err1, $err2 ], $errors[null]);
		$this->assertSame([ $err3, $err4 ], $errors[$attribute]);

		unset($errors[null]);
		$this->assertSame([], $errors[null]);
		unset($errors[$attribute]);
		$this->assertSame([], $errors[$attribute]);
	}

	public function test_iterator()
	{
		$errors = $this->errors;
		$err1 = new Error('err1-' . uniqid());
		$err2 = new Error('err2-' . uniqid());
		$err3 = new Error('err3-' . uniqid());
		$err4 = new Error('err4-' . uniqid());
		$attribute = uniqid();
		$errors[$attribute] = $err3;
		$errors[] = $err1;
		$errors[$attribute] = $err4;
		$errors[] = $err2;

		$iterator_errors = [];

		foreach ($errors as $a => $e)
		{
			$iterator_errors[] = [ $a => $e ];
		}

		$this->assertSame([

			[ ErrorCollection::GENERIC => $err1 ],
			[ ErrorCollection::GENERIC => $err2 ],
			[ $attribute => $err3 ],
			[ $attribute => $err4 ],

		], $iterator_errors);
	}

	public function test_each()
	{
		$errors = $this->errors;
		$err1 = new Error('err1-' . uniqid());
		$err2 = new Error('err2-' . uniqid());
		$err3 = new Error('err3-' . uniqid());
		$err4 = new Error('err4-' . uniqid());
		$attribute = uniqid();
		$errors[$attribute] = $err3;
		$errors[] = $err1;
		$errors[$attribute] = $err4;
		$errors[] = $err2;

		$iterator_errors = [];

		$errors->each(function ($error, $attribute, $collection) use (&$iterator_errors, $errors) {

			$this->assertSame($errors, $collection);
			$iterator_errors[] = [ $attribute => $error ];

		});

		$this->assertSame([

			[ ErrorCollection::GENERIC => $err1 ],
			[ ErrorCollection::GENERIC => $err2 ],
			[ $attribute => $err3 ],
			[ $attribute => $err4 ],

		], $iterator_errors);
	}

	public function test_clear()
	{
		$errors = $this->errors->add(uniqid())->add(uniqid())->add(uniqid());
		$this->assertEquals(3, $errors->count());
		$this->assertEquals(0, $errors->clear()->count());
	}

	public function test_merge()
	{
		$er1 = new Error(uniqid());
		$er2 = new Error(uniqid());

		$col1 = (new ErrorCollection)
			->add_generic($er1);
		$col2 = (new ErrorCollection)
			->add_generic($er2);

		$col1->merge($col2);

		$this->assertSame([

			ErrorCollection::GENERIC => [

				$er1, $er2

			]

		], $col1->to_array());
	}

	public function test_json_serialize()
	{
		$format = "error: {arg}";
		$arg1 = uniqid();
		$arg2 = uniqid();
		$arg3 = uniqid();
		$arg4 = uniqid();
		$attribute = uniqid();
		$errors = $this->errors;
		$errors->add($attribute, $format, [ 'arg' => $arg3 ]);
		$errors->add_generic($format, [ 'arg' => $arg1 ]);
		$errors->add($attribute, $format, [ 'arg' => $arg4 ]);
		$errors->add_generic($format, [ 'arg' => $arg2 ]);

		$this->assertSame(json_encode([

			ErrorCollection::GENERIC => [

				"error: {$arg1}",
				"error: {$arg2}",

			],

			$attribute => [

				"error: {$arg3}",
				"error: {$arg4}",

			]

		]), json_encode($errors));
	}
}
