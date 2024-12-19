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

namespace Philiagus\Figment\Container\Resolver;

use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Configuration\Registrable;
use Philiagus\Figment\Container\Contract\Resolver;
use Traversable;

class InstanceObject implements Resolver, Registrable, \IteratorAggregate
{

    /**
     * @param object $object
     */
    public function __construct(
        private Configuration $configuration,
        private object $object
    )
    {
    }

    public function resolve(): object
    {
        return $this->object;
    }

    public function registerAs(string ...$id): Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    public function getIterator(): Traversable
    {
        yield $this;
    }
}
