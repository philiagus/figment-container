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
use Philiagus\Figment\Container\Contract\Builder\ConstructorBuilder;
use Philiagus\Figment\Container\Contract\Builder\GeneratorBuilder;
use Philiagus\Figment\Container\Contract\Builder\InjectionBuilder;
use Philiagus\Figment\Container\Contract\Builder\ListBuilder;
use Philiagus\Figment\Container\Contract\Builder\ObjectBuilder;

interface Configuration extends BuilderContainer
{

    public function register(Builder $builder, string ...$id): self;

    /**
     * Provides a builder that will create the class using its constructor
     *
     * @param class-string $className
     *
     * @return InjectionBuilder
     */
    public function injected(string $className): InjectionBuilder;

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
     * @return ConstructorBuilder
     * @see self::injected()
     * @see DisableSingleton
     * @see EagerInstantiation
     * @see ConstructorBuilder
     */
    public function constructed(string $className): ConstructorBuilder;

    /**
     * Provides a builder that will use the provided closure in order to create
     * the required service
     *
     * @param \Closure(Container $container): object $closure
     *
     * @return GeneratorBuilder
     *
     * @see GeneratorBuilder
     */
    public function generator(\Closure $closure): GeneratorBuilder;

    public function object(object $object): ObjectBuilder;

    public function list(?string $id = null): ListBuilder;
}
