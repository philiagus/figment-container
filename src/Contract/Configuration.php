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

use Philiagus\Figment\Container\Contract\Instance\InstanceConfigurator;
use Philiagus\Figment\Container\Contract\List\InstanceList;
use Philiagus\Figment\Container\Contract\List\ListConfigurator;

/**
 * Implementing classes can build a configuration for a container.
 * Think of this configuration as a factory for container
 */
interface Configuration
{
    /**
     * Creates a configurator for a class that can be instanced by
     * the container. Any class the container wants to instance or
     * interact with must implement the Injectable interface
     *
     * @param class-string<Injectable> $className
     * @return InstanceConfigurator
     * @see Injectable
     */
    public function class(string $className): InstanceConfigurator;

    /**
     * Creates a list configuration that can later be exposed under a given name
     *
     * You can fill in the desired list contents using the parameters of this
     * method or calling configuring methods on the returned ListConfigurator
     *
     * When the configured list is resolved and injected it will result in an
     * InstanceList object that lazily resolves its contents
     *
     * @param Resolvable ...$content
     * @return ListConfigurator
     * @see InstanceList
     */
    public function list(Resolvable ...$content): ListConfigurator;

    /**
     * Allows to define the creation of an object on the fly instead of using
     * a class. The closure receives an injector that is resolved after the closure is done.
     *
     *  It is highly discouraged to create object with side effects using this method,
     *  instead opting for a provider-class through the instanceClass method.
     *
     *  The reason being that the framework fundamentally wants to be lazy in everything
     *  it does, which is inhibited by creating for example a database connection in
     *  order to even create the configuration.
     *
     * @param \Closure(Injector):object $generator
     * @return InstanceConfigurator
     */
    public function generator(\Closure $generator): InstanceConfigurator;

    /**
     * Allows to expose an already created object using the container without having
     * to go through creating a class or provider for that object.
     *
     * It is highly discouraged to create object with side effects using this method,
     * instead opting for a provider-class through the instanceClass method.
     *
     * The reason being that the framework fundamentally wants to be lazy in everything
     * it does, which is inhibited by creating for example a database connection in
     * order to even create the configuration.
     *
     *
     * @param object $object
     * @return Exposable
     */
    public function object(object $object): Exposable;

    /**
     * Returns true if a service with the given id is registered
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * Returns a resolvable object that can create the targeted service
     * @param string $id
     * @return Resolvable
     */
    public function get(string $id): Resolvable;

    /**
     * Exposes the provided Resolvable under the given id.
     * This method is rarely called directly and should instead be called using the
     * chained calling for configurations
     *
     * @param Resolvable $resolvable
     * @param string ...$id
     * @return self
     * @see self::object()
     * @see self::class()
     * @see self::list()
     * @see self::generator()
     * @see Exposable::exposeAs()
     */
    public function expose(Resolvable $resolvable, string ...$id): self;

    /**
     * Creates a container instance that points to this configuration
     *
     * @param string|null $exposeContainerAs If provided the container will be exposed under the provided name
     * @return Container
     */
    public function buildContainer(?string $exposeContainerAs = null): Container;
}
