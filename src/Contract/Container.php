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

namespace Philiagus\Figment\Container\Contract;

use Philiagus\Figment\Container\Contract\List\InstanceList;

/**
 * Base interface of any container created by the framework.
 *
 * A container usually wraps a container configuration and allows access to
 * its contents using the resolve methods
 */
interface Container extends ResolverProvider
{

    /**
     * Resolves the requested list and returns the InstanceList result.
     *
     * If no list of the given name is exposed an exception is thrown.
     *
     * @param string $name
     * @return InstanceList
     * @throws \OutOfBoundsException
     * @see List\ListExposer::exposeAs()
     */
    public function list(string $name): InstanceList;

    /**
     * Resolves the requested instance
     *
     * If not instance of the given name is exposed an exception if thrown
     *
     * @param string $name
     * @param bool $disableSingleton If true the resolver will create a
     *                                new object instead of returning a
     *                                possibly already existing one
     * @return object
     * @throws \OutOfBoundsException
     */
    public function instance(string $name, bool $disableSingleton = false): object;
}
