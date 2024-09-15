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

namespace Philiagus\Figment\Container\Contract\List;

use Philiagus\Figment\Container\Contract\Exposable;
use Philiagus\Figment\Container\Contract\Resolvable;

/**
 * Implementing classes are defined as list configuration classes
 */
interface ListConfigurator extends Resolvable, Exposable
{

    /**
     * Add the specified list of resolvers to the end of the list.
     * This method will ignore named parameters, so setting names for the instances
     * is not possible using this method.
     *
     * @param Resolvable ...$instance
     * @return self
     */
    public function append(Resolvable ...$instance): self;
}
