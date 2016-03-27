# Errors

[![Release](https://img.shields.io/packagist/v/icanboogie/errors.svg)](https://packagist.org/packages/icanboogie/errors)
[![Build Status](https://img.shields.io/travis/ICanBoogie/Errors/master.svg)](http://travis-ci.org/ICanBoogie/Errors)
[![HHVM](https://img.shields.io/hhvm/icanboogie/errors.svg)](http://hhvm.h4cc.de/package/icanboogie/errors)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/Errors/master.svg)](https://scrutinizer-ci.com/g/ICanBoogie/Errors)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Errors/master.svg)](https://coveralls.io/r/ICanBoogie/Errors)
[![Packagist](https://img.shields.io/packagist/dt/icanboogie/errors.svg)](https://packagist.org/packages/icanboogie/errors)

A library to collect errors.





### Usage

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





----------





## Requirements

The package requires PHP 5.3 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/):

```
$ composer require icanboogie/errors
```





### Cloning the repository

The package is [available on GitHub](https://github.com/ICanBoogie/Errors), its repository can
be cloned with the following command line:

	$ git clone https://github.com/ICanBoogie/Errors.git





## Documentation

The package is documented as part of the [ICanBoogie][] framework
[documentation][]. You can generate the documentation for the package and its dependencies with
the `make doc` command. The documentation is generated in the `build/docs` directory.
[ApiGen](http://apigen.org/) is required. The directory can later be cleaned with
the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [PHPUnit](https://phpunit.de/) and
[Composer](http://getcomposer.org/) need to be globally available to run the suite.
The command installs dependencies as required. The `make test-coverage` command runs test suite
and also creates an HTML coverage report in "build/coverage". The directory can later be cleaned
with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/ICanBoogie/Errors/master.svg)](https://travis-ci.org/ICanBoogie/Errors)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Errors/master.svg)](https://coveralls.io/r/ICanBoogie/Errors)





## License

**icanboogie/errors** is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.





[documentation]:               http://api.icanboogie.org/errors/1.0/
[ICanBoogie]:                  https://github.com/ICanBoogie/ICanBoogie
