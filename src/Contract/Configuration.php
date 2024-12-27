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

use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Attribute\EagerInstantiation;
use Philiagus\Figment\Container\Contract\Builder\Registrable;

interface Configuration extends BuilderContainer
{

    /**
     * Registers the provided builder under the list of given IDs.
     *
     * This method will most likely never be called directly, but instead by the
     * registerAs method of the builders.
     *
     * @param Builder $builder
     * @param non-empty-string ...$id
     *
     * @return self
     * @see Registrable::registerAs()
     */
    public function register(Builder $builder, string ...$id): self;

    /**
     * Provides a builder that will create the class using its constructor with
     * default parameter values taken via Attributes
     *
     * @param class-string $className
     *
     * @return Builder\InjectionBuilder
     */
    public function injected(string $className): Builder\InjectionBuilder;

    /**
     * Provides a builder that will use the constructor (if exists) of the
     * defined class in order to create an instance of the required class
     * without taking Injection Attributes into account. If you want to use
     * injection attributes and only overwrite individual constructor parameters
     * please use the injected($className) and configure your desired injections
     * there.
     *
     *
     * @param class-string $className
     *
     * @return Builder\ConstructorBuilder
     * @see self::injected()
     * @see DisableSingleton
     * @see EagerInstantiation
     * @see Builder\ConstructorBuilder
     */
    public function constructed(string $className): Builder\ConstructorBuilder;

    /**
     * Provides a builder that will use the provided closure in order to create
     * the required instance
     *
     * @param \Closure(Container $container, string $name): object $closure
     *
     * @return Builder\ClosureBuilder
     *
     * @see Builder\ClosureBuilder
     */
    public function closure(\Closure $closure): Builder\ClosureBuilder;

    /**
     * Provides a builder that will use the provided Factory to create the
     * required instance.
     *
     * If the provided factory is a string, the container will resolve that
     * string as $id, requesting the instance from the container, which must
     * yield a Factory
     *
     * @param string|Factory $factory
     *
     * @return Builder\FactoryBuilder
     */
    public function factory(string|Factory $factory): Builder\FactoryBuilder;

    /**
     * Provides a Builder that can register the provided object in the container
     *
     * @param object $object
     *
     * @return Builder\ObjectBuilder
     */
    public function object(object $object): Builder\ObjectBuilder;

    /**
     * Provides a builder that allows you to create and expose a list under the
     * provided name.
     *
     * If the $id is provided, the Builder will make sure the list is registered
     * under the given $id. If such a list already exists the returned builder
     * will be a builder of that list to make it easier to add values to lists
     * from any parts of the configuration code.
     *
     * @param null|string $id
     *
     * @return Builder\ListBuilder
     */
    public function list(?string $id = null): Builder\ListBuilder;
}
