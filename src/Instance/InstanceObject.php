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

namespace Philiagus\Figment\Container\Instance;

use Philiagus\Figment\Container\Contract;

readonly class InstanceObject implements Contract\Instance\InstanceResolver, Contract\Instance\InstanceExposer
{

    public function __construct(
        private \Closure $exposer,
        private object   $object
    )
    {
    }

    public function resolve(bool $disableSingleton = false): object
    {
        return $this->object;
    }

    public function exposeAs(string $name): Contract\Instance\InstanceExposer
    {
        ($this->exposer)($name, $this);

        return $this;
    }
}
