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

use Philiagus\Figment\Container\Contract\Configuration\ConstructedConfigurator;
use Philiagus\Figment\Container\Contract\Configuration\InjectionConfigurator;
use Philiagus\Figment\Container\Contract\Configuration\ListConfigurator;
use Philiagus\Figment\Container\Resolver\InstanceInjected;
use Philiagus\Figment\Container\Resolver\Proxy\LazyResolvable;

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
     * @return InjectionConfigurator
     * @see Injectable
     */
    public function injected(string $className): Contract\Configuration\InjectionConfigurator
    {
        return new InstanceInjected($this, $className);
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

    public function object(object $object): Contract\Configuration\Registrable&Contract\Resolver
    {
        return new Resolver\InstanceObject($this, $object);
    }

    public function generator(bool $useSingleton, \Closure $closure): Contract\Configuration\Registrable&Contract\Resolver
    {
        return new Resolver\InstanceGenerator($this, $useSingleton, $closure);
    }

    public function register(Contract\Resolver $resolver, string ...$id): self
    {
        foreach ($id as $singleId) {
            if(isset($this->registered[$singleId]) && $this->registered[$singleId] !== $resolver) {
                throw new ContainerException("Trying to register two services with the same id '$singleId'");
            }
            $this->registered[$singleId] = $resolver;
        }

        return $this;
    }

    public function list(?string $id = null): ListConfigurator
    {
        if($id === null)
            return new Resolver\ListConfiguration($this);

        $registeredList = $this->registered[$id] ?? null;
        if($registeredList === null) {
            return $this->registered[$id] = new Resolver\ListConfiguration($this);
        }
        if($registeredList instanceof ListConfigurator) {
            return $registeredList;
        }

        throw new ContainerException(
            "Trying to access '$id' as list, which is already registered as not being a list"
        );
    }

    public function constructed(string $className): ConstructedConfigurator
    {
        return new Resolver\InstanceConstructed($this, $className);
    }
}
