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

use Philiagus\Figment\Container\Contract\Instance\InstanceConfigurator;
use Philiagus\Figment\Container\Resolver\InstanceClass;

class Configuration implements Contract\Configuration
{

    private array $registered = [];

    private array $lazies = [];

    private readonly Contract\Context $context;

    public function __construct(
        Contract\Context|array $context = []
    )
    {
        if ($context instanceof Contract\Context) {
            $this->context = $context;
        } else {
            $this->context = new Context\ArrayContext($context);
        }
    }

    /**
     * Creates a configurator for a class that can be instanced by
     * the container. Any class the container wants to instance or
     * interact with must implement the Injectable interface
     *
     * @param class-string $className
     * @return InstanceConfigurator
     * @see Injectable
     */
    public function class(string $className): Contract\Instance\InstanceConfigurator
    {
        return new InstanceClass($this, $this->context, $className);
    }

    /** @inheritDoc */
    public function has(string $id): bool
    {
        return isset($this->registered[$id]);
    }

    /** @inheritDoc */
    public function get(string $id): Contract\Resolver
    {
        return $this->registered[$id] ?? $this->lazies[$id] ??= new LazyResolvable($this, $id);
    }

    public function context(): Contract\Context
    {
        return $this->context;
    }

    public function buildContainer(): Contract\Container
    {
        $container = new Container($this);
        $this
            ->object($container)
            ->registerAs('container');
        return $container;
    }

    private function object(object $object): Contract\Registrable
    {
        return new Resolver\InstanceObject($this, $object);
    }

    /**
     * @param \Closure(Contract\Provider): object $closure
     * @return Contract\Registrable
     */
    public function generator(bool $useSingleton, \Closure $closure): Contract\Registrable
    {
        return new Resolver\InstanceGenerator($this, $useSingleton, $closure);
    }

    public function register(Contract\Resolver $resolver, string ...$id)
    {
        foreach ($id as $singleId) {
            $this->registered[$singleId] = $resolver;
        }
    }
}
