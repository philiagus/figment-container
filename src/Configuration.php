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

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Contract\Resolvable;
use Philiagus\Figment\Container\Resolvable\InstanceClass;
use Philiagus\Figment\Container\Resolvable\InstanceGenerator;
use Philiagus\Figment\Container\Resolvable\InstanceObject;
use Philiagus\Figment\Container\Resolvable\ListConfigurator;

class Configuration implements Contract\Configuration
{

    private array $exposed = [];

    private array $lazies = [];

    /** @inheritDoc */
    public function expose(Resolvable $resolvable, string ...$id): self
    {
        foreach($id as $individualId) {
            if (isset($this->exposed[$individualId]))
                throw new \LogicException(
                    "Trying to expose under the name '$individualId', which is already in use"
                );

            $this->exposed[$individualId] = $resolvable;
        }

        return $this;
    }

    /** @inheritDoc */
    public function object(object $object): Contract\Resolvable&Contract\Exposable
    {
        return new InstanceObject($this, $object);
    }

    /** @inheritDoc */
    public function class(string $className): Contract\Instance\InstanceConfigurator
    {
        return new InstanceClass($this, $className);
    }

    /** @inheritDoc */
    public function list(Contract\Resolvable ...$content): Contract\List\ListConfigurator
    {
        return new ListConfigurator($this, ...$content);
    }

    /** @inheritDoc */
    public function generator(\Closure $generator): Contract\Instance\InstanceConfigurator
    {
        return new InstanceGenerator($this, $generator);
    }

    /** @inheritDoc */
    public function has(string $id): bool
    {
        return isset($this->exposed[$id]);
    }

    /** @inheritDoc */
    public function get(string $id): Resolvable
    {
        return $this->exposed[$id] ?? $this->lazies[$id] ??= new LazyResolvable($this, $id);
    }

    /** @inheritDoc */
    public function buildContainer(?string $exposeContainerAs = null): Contract\Container
    {
        return new Container($this, $exposeContainerAs);
    }
}
