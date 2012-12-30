# Errors

An error collector.





## Requirement

PHP 5.3+ is required.





## Installation

The easiest way to install the package is to use [composer](http://getcomposer.org/).
Just create a `composer.json` file and run the `php composer.phar install` command.

```json
{
	"minimum-stability": "dev",
	"require":
	{
		"icanboogie/errors": "*"
	}
}
```






### Cloning the repository

The package is [available on GitHub](https://github.com/ICanBoogie/Errors), its repository can be
cloned with the following command line:

	$ git clone git://github.com/ICanBoogie/Errors.git





## Usage

```php
<?php

use ICanBoogie\Errors;

$e = new Errors();

var_dump($e['password']);
// null

$e['password'] = 'Invalid password';
var_dump($e['password']);
// 'Invalid password'

$e['password'] = 'Ugly password';
var_dump($e['password']);
// array('Invalid password', 'Ugly password')

$e[] = 'General error.';
count($e)
// 3

$e->each(function($name, $message) {

	echo "$name: $message\n";

});
// :General error.
// password: Invalid password
// password: Ugly password
```





## Documentation

The package is documented as part of the [ICanBoogie](http://icanboogie.org/) framework
[documentation](http://icanboogie.org/docs/). You can generate the documentation for the package
and its dependencies with the `make doc` command. The documentation is generated in the `docs`
directory. [ApiGen](http://apigen.org/) is required. You can later clean the directory with
the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all dependencies required to run the suite. You can later
clean the directory with the `make clean` command.





## License

ICanBoogie/Errors is licensed under the New BSD License - See the LICENSE file for details.