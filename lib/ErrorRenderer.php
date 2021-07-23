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
 * An interface for a callable used to render errors.
 */
interface ErrorRenderer
{
    /**
     * Renders an error into a string.
     */
    public function __invoke(Error $error, string $attribute, ErrorCollection $collection): string;
}
