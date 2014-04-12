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
 * An error collector.
 */
class Errors implements \ArrayAccess, \Countable, \Iterator
{
	static public $message_constructor;

	protected $errors = array(null => array());

	/**
	 * Checks if an error is defined for an attribute.
	 *
	 * Example:
	 *
	 * <pre>
	 * $e = new Errors();
	 * $e['username'] = 'Funny username';
	 * var_dump(isset($e['username']);
	 * #=> true
	 * var_dump(isset($e['password']);
	 * #=> false
	 * </pre>
	 *
	 * @return boolean true if an error is defined for the specified attribute, false otherwise.
	 */
	public function offsetExists($attribute)
	{
		return isset($this->errors[$attribute]);
	}

	/**
	 * Returns error messages.
	 *
	 * Example:
	 *
	 * <pre>
	 * $e = new Errors();
	 * var_dump($e['password']);
	 * #=> null
	 * $e['password'] = 'Invalid password';
	 * var_dump($e['password']);
	 * #=> 'Invalid password'
	 * $e['password'] = 'Ugly password';
	 * var_dump($e['password']);
	 * #=> array('Invalid password', 'Ugly password')
	 * </pre>
	 *
	 * @param string|null $attribute The attribute that caused the error, or null if the error is global.
	 *
	 * @return string|array|null Return the global error messages or the error messages attached
	 * to an attribute. If there is only one message a string is returned, otherwise an array
	 * with all the messages is returned. null is returned if there is no message defined.
	 */
	public function offsetGet($attribute)
	{
		if (empty($this->errors[$attribute]))
		{
			return null;
		}

		$messages = $this->errors[$attribute];

		return count($messages) > 1 ? $messages : current($messages);
	}

	/**
	 * Adds an error message.
	 *
	 * Example:
	 *
	 * <pre>
	 * $e = new Errors();
	 * $e['password'] = 'Invalid password';
	 * $e[] = 'Requires authentication';
	 * </pre>
	 *
	 * @param string|null $attribute If null, the message is considered as a general error message
	 * instead of an attribute message.
	 * @param string $message The error message.
	 */
	public function offsetSet($attribute, $message)
	{
		$this->errors[$attribute][] = $message;
	}

	/**
	 * Removes error messages.
	 *
	 * @param string|null attribute If null, general message are removed, otherwise the message
	 * attached to the attribute are removed.
	 */
	public function offsetUnset($attribute)
	{
		unset($this->errors[$attribute]);
	}

	/**
	 * Returns the number of errors defined.
	 *
	 * Example:
	 *
	 * <pre>
	 * $e = new Errors();
	 * $e['username'] = 'Funny user name';
	 * $e['password'] = 'Weak password';
	 * $e['password'] = 'should have at least one digit';
	 * count($e);
	 * #=> 3
	 * </pre>
	 */
	public function count()
	{
		$n = 0;

		foreach ($this->errors as $errors)
		{
			$n += count($errors);
		}

		return $n;
	}

	private $i;
	private $ia;

	public function current()
	{
		return $this->ia[$this->i][1];
	}

	public function next()
	{
		++$this->i;
	}

	public function key()
	{
		return $this->ia[$this->i][0];
	}

	public function valid()
	{
		return isset($this->ia[$this->i]);
	}

	public function rewind()
	{
		$this->i = 0;
		$ia = array();

		foreach ($this->errors as $attribute => $errors)
		{
			foreach ($errors as $error)
			{
				$ia[] = array($attribute, $error);
			}
		}

		$this->ia = $ia;
	}

	/**
	 * Iterates through errors using the specified callback.
	 *
	 * Example:
	 *
	 * <pre>
	 * $e = new Errors();
	 * $e['username'] = 'Funny user name';
	 * $e['password'] = 'Weak password';
	 *
	 * $e->each(function($attribute, $message) {
	 *
	 *     echo "$attribute => $message<br />";
	 *
	 * });
	 * </pre>
	 *
	 * @param mixed $callback
	 */
	public function each($callback)
	{
		foreach ($this->errors as $attribute => $errors)
		{
			foreach ($errors as $error)
			{
				call_user_func($callback, $attribute, $error);
			}
		}
	}

	/**
	 * Clears the errors.
	 */
	public function clear()
	{
		$this->errors = array(null => array());
	}

	/**
	 * Formats the given string by replacing placeholders with the values provided.
	 *
	 * @param string $pattern The format pattern.
	 * @param array $args An array of replacements for the placeholders.
	 * @param array $options Options for the formatter.
	 *
	 * @return mixed A string or a stringyfiable object.
	 *
	 * @see \ICanBoogie\I18n\FormattedString
	 * @see \ICanBoogie\FormattedString
	 * @see \ICanBoogie\format
	 */
	public function format($pattern, array $args=array(), array $options=array())
	{
		if (!self::$message_constructor)
		{
			$constructors = array('ICanBoogie\I18n\FormattedString', 'ICanBoogie\FormattedString');

			foreach ($constructors as $constructor)
			{
				if (class_exists($constructor, true))
				{
					self::$message_constructor = $constructor;

					break;
				}
			}
		}

		$constructor = self::$message_constructor;

		if (!$constructor)
		{
			return \ICanBoogie\format($pattern, $args);
		}

		return new $constructor($pattern, $args, $options);
	}
}