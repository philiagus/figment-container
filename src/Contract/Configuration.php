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

use Philiagus\Figment\Container\Contract\Builder\ConstructorBuilder;
use Philiagus\Figment\Container\Contract\Builder\GeneratorBuilder;
use Philiagus\Figment\Container\Contract\Builder\InjectionBuilder;
use Philiagus\Figment\Container\Contract\Builder\ListBuilder;
use Philiagus\Figment\Container\Contract\Builder\ObjectBuilder;

interface Configuration extends BuilderContainer
{

    public function register(Builder $builder, string ...$id): self;

    /**
     * Creates a configurator for a class that can be instanced by
     * the container. Any class the container wants to instance or
     * interact with must implement the Injectable interface
     *
     * @param class-string $className
     * @return InjectionBuilder
     * @see Injectable
     */
    public function injected(string $className): InjectionBuilder;

    public function constructed(string $className): ConstructorBuilder;

    /**
     * @param \Closure(Container $container): object $closure
     * @return GeneratorBuilder
     */
    public function generator(\Closure $closure): GeneratorBuilder;

    public function object(object $object): ObjectBuilder;

    public function list(?string $id = null): ListBuilder;
}
