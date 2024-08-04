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
use Philiagus\Figment\Container\Contract\Instance\InstanceExposer;
use Philiagus\Figment\Container\Contract\Instance\InstanceResolver;
use Philiagus\Figment\Container\Contract\List\ListConfigurator;
use Philiagus\Figment\Container\Contract\List\ListResolver;

/**
 * Implementing classes can build a configuration for a container.
 * Think of this configuration as a factory for container
 */
interface Configuration extends ResolverProvider
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
    public function instanceClass(string $className): InstanceConfigurator;

    /**
     * Creates a list configuration that can later be exposed under a given name
     *
     * You can fill in the desired list contents using the parameters of this
     * method or calling configuring methods on the returned ListConfigurator
     *
     * @param InstanceResolver|ListResolver ...$content
     * @return ListConfigurator
     */
    public function list(InstanceResolver|ListResolver ...$content): ListConfigurator;

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
    public function instanceGenerator(\Closure $generator): InstanceConfigurator;

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
     * @return InstanceExposer
     */
    public function instanceObject(object $object): InstanceExposer;
}
