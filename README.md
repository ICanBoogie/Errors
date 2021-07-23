# Errors

[![Release](https://img.shields.io/packagist/v/icanboogie/errors.svg)](https://packagist.org/packages/icanboogie/errors)
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



## Continuous Integration

The project is continuously tested by [GitHub actions](https://github.com/ICanBoogie/Erorrs/actions).

[![Tests](https://github.com/ICanBoogie/Errors/workflows/test/badge.svg?branch=master)](https://github.com/ICanBoogie/Errors/actions?query=workflow%3Atest)
[![Static Analysis](https://github.com/ICanBoogie/Errors/workflows/static-analysis/badge.svg?branch=master)](https://github.com/ICanBoogie/Errors/actions?query=workflow%3Astatic-analysis)
[![Code Style](https://github.com/ICanBoogie/Errors/workflows/code-style/badge.svg?branch=master)](https://github.com/ICanBoogie/Errors/actions?query=workflow%3Acode-style)



## Documentation

The package is documented as part of the [ICanBoogie][] framework
[documentation][]. You can generate the documentation for the package and its dependencies with
the `make doc` command. The documentation is generated in the `build/docs` directory.
[ApiGen](http://apigen.org/) is required. The directory can later be cleaned with
the `make clean` command.



## Testing

We provide a Docker container for local development. Run `make test-container` to create a new session. Inside the
container run `make test` to run the test suite. Alternatively, run `make test-coverage` for a breakdown of the code
coverage. The coverage report is available in `build/coverage/index.html`.



## License

**icanboogie/errors** is released under the [BSD-3-Clause](LICENSE).



[documentation]:               https://icanboogie.org/api/errors/latest/
[ICanBoogie]:                  https://icanboogie.org/
