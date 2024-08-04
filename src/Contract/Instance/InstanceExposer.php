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
 * Classes implementing this interface can be exposed as instances by the container
 */
interface InstanceExposer
{

    /**
     * Exposes the instance under the given name to other Injectables.
     * No two services can be exposed under the same name at the same time!
     *
     * @param string $name
     * @return InstanceExposer
     * @throws \LogicException
     */
    public function exposeAs(string $name): InstanceExposer;

}
