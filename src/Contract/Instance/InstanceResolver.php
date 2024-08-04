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

namespace Philiagus\Figment\Container\Contract\Instance;

/**
 * Implementing classes are able to create objects based in their configuration
 */
interface InstanceResolver
{
    /**
     * Trigger this class to resolve its content and making available an instance
     * of the class as defined.
     *
     * @param bool $disableSingleton If true the resolver will create a
     *                               new object instead of returning a
     *                               possibly already existing one
     * @return object
     */
    public function resolve(bool $disableSingleton = false): object;
}
