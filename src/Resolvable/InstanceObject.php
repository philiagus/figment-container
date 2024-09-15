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

namespace Philiagus\Figment\Container\Resolvable;

use Philiagus\Figment\Container\Contract;
use Traversable;

/**
 * @internal
 */
readonly class InstanceObject implements Contract\Resolvable, Contract\Exposable, \IteratorAggregate
{

    public function __construct(
        private Contract\Configuration $configuration,
        private object                 $object
    )
    {
    }

    public function resolve(): object
    {
        return $this->object;
    }

    public function exposeAs(string ...$id): Contract\Exposable
    {
        $this->configuration->expose($this, ...$id);

        return $this;
    }

    public function getIterator(): Traversable
    {
        throw new \LogicException("Trying to iterate over a single object instance resolver");
    }
}
