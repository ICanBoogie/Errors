<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\ICanBoogie;

use ICanBoogie\Error;
use PHPUnit\Framework\TestCase;

final class ErrorTest extends TestCase
{
    public function test_getters(): void
    {
        $format = uniqid();
        $args = [ uniqid() => uniqid() ];
        $error = new Error($format, $args);

        $this->assertSame($format, $error->format);
        $this->assertSame($args, $error->args);
    }
}
