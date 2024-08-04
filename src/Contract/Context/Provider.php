<?php
/*
 * This file is part of philiagus/figment-container
 *
 * (c) Andreas Eicher <philiagus@philiagus.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Context;

use Philiagus\Figment\Container\Contract\Injector;

/**
 * Implementing classes provide context information
 * Context is defined as a list of configurations (such as environment variables, hard coded configuration, etc..)
 * which is provided to injectable classes on a name bases and can be consumed using the Injector
 *
 * @see Injector::parseContext()
 * @see Injector::context()
 */
interface Provider
{

    /**
     * Returns true if the provider has a configuration for the defined name
     * Please be aware that - unlike isset calls on arrays - this returns true
     * even if the configuration is set to null
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Returns the requested configuration by name or throws an exception on error
     * @param string $name
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function get(string $name): mixed;
}
