# Errors

[![Release](https://img.shields.io/packagist/v/icanboogie/errors.svg)](https://packagist.org/packages/icanboogie/errors)
[![Build Status](https://img.shields.io/travis/ICanBoogie/Errors/master.svg)](http://travis-ci.org/ICanBoogie/Errors)
[![HHVM](https://img.shields.io/hhvm/icanboogie/errors.svg)](http://hhvm.h4cc.de/package/icanboogie/errors)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/Errors/master.svg)](https://scrutinizer-ci.com/g/ICanBoogie/Errors)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/Errors/master.svg)](https://coveralls.io/r/ICanBoogie/Errors)
[![Packagist](https://img.shields.io/packagist/dt/icanboogie/errors.svg)](https://packagist.org/packages/icanboogie/errors)

Collects formatted errors.



## Installation

```bash
composer require icanboogie/errors
```



## Usage

```php
<?php

use ICanBoogie\ErrorCollection;

$errors = new ErrorCollection;

var_dump($errors['password']);
// null

$errors->add('password');
var_dump($errors['password']);
// [ Error{ format: '', params: [] } ]

$errors->add('password', 'Invalid password: {value}', [ 'value' => "123" ]);
var_dump($errors['password']);
// 'Invalid password: 123'

$errors['password'] = 'Ugly password';
var_dump($errors['password']);
// array('Invalid password', 'Ugly password')

$errors->add_generic('General error');
count($errors);
// 3

$errors->each(function($name, $message) {

    echo "$name: $message\n";

});
// General error
// password: Invalid password
// password: Ugly password
```





----------



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

**icanboogie/errors** is released under the [BSD-3-Clause](LICENSE).



[documentation]:               https://icanboogie.org/api/errors/2.0/
[ICanBoogie]:                  https://icanboogie.org/
