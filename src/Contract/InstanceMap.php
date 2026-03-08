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

use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Philiagus\Figment\Container\Exception\NotFoundException;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

interface InstanceMap extends \Traversable, \Countable
{


    /**
     * Iterates through the builders
     *
     * @template TResult as object
     *
     * @param null|class-string<TResult>|class-string[]|\Closure(object $object): bool $type
     *
     * @return \Iterator<mixed, Builder<TResult>>
     */
    public function traverseBuilders(null|\Closure|string|array $type = null): \Traversable;

    /**
     * Traverses through the resolved instances of this list
     * Every iteration must call the builder again, leaving singleton handling to
     * the instances
     *
     * This must also be the default \Traversable when iterating this object itself
     * with $type = null
     *
     * @template TResult as object
     * @param null|class-string<TResult>|class-string[]|\Closure(object $object): bool $type
     *
     * @return \Iterator<mixed, TResult>
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PrependMessageThrowableInterface
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function traverseInstances(null|\Closure|string|array $type = null): \Traversable;

    /**
     * Returns true if the provided key exists in the InstanceMap
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has(mixed $key): bool;

    /**
     * Returns the list of defined keys for the map
     * @return array
     */
    public function keys(): array;

    /**
     * Returns a new instance of the defined key
     *
     * If $type is provided the container will check that the object is actually of the desired type
     * and throw an exception if not
     *
     * @template TResult as object
     * @param mixed $key
     * @param null|class-string<TResult>|class-string[]|\Closure(object $object): bool $type
     *
     * @return TResult
     * @throws ContainerException if the key is not defined
     */
    public function getInstance(mixed $key, null|\Closure|string|array $type = null): object;

    /**
     * Returns the builder for the specified key
     *
     *  If $type is provided the builder will check that the object is actually of the desired type
     *  and throw an exception if not
     *
     * @template TResult as object
     * @param mixed $key
     * @param null|class-string<TResult>|class-string[]|\Closure(object $object): bool $type
     *
     * @return Builder
     * @throws ContainerException if the key is not defined
     */
    public function getBuilder(mixed $key, null|\Closure|string|array $type = null): Builder;

}
