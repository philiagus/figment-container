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

use Closure;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Philiagus\Figment\Container\Exception\NotFoundException;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

interface Container extends ContainerInterface, ContextProvider
{

    /**
     * @param string $id
     * @param null|class-string<TResult> $className = null
     *
     * @return TResult
     *
     * @template TResult as object
     *
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PrependMessageThrowableInterface
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function get(string $id, ?string $className = null): object;

    /**
     * Works by invoking self::get with identical $id and $class
     *
     * @param class-string<T> $className
     *
     * @return T
     *
     * @template T
     *
     * @see self::get()
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PrependMessageThrowableInterface
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function class(string $className): object;

    /**
     * @param string $id
     *
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PrependMessageThrowableInterface
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function has(string $id): bool;

    /**
     * Prepares the closure for invocation, treating correspondingly
     * annotated elements as something that will be injected by the container.
     *
     * The container makes sure it can instantiate the elements marked for injection
     * by attributes right now. You can call the function later.
     *
     * You can provide a list of later provided arguments. These arguments
     * will be ignored by the container, even if annotations are present. You'll
     * have to provide the value for these parameters yourself when invoking the prepared function
     *
     * @param Closure $closure
     * @param array<string, mixed> $definedArguments
     * @param array $laterProvidedArguments
     *
     * @return PreparedFunction
     */
    public function prepare(
        \Closure $closure,
        array $definedArguments = [],
        array $laterProvidedArguments = []
    ): PreparedFunction;

    /**
     * @param \Closure $closure
     * @param array<string, mixed> $additionalArguments
     *
     * @return mixed
     */
    public function invoke(\Closure $closure, array $additionalArguments): mixed;

    /**
     * Creates a new instance of the targeted class, setting parameters via attributes if they are not set by
     * the list of provided parameters
     *
     * @param class-string<TResult> $className
     * @param array<string, mixed> $parameters
     *
     * @return TResult
     *
     * @template TResult as object
     *
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PrependMessageThrowableInterface
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function instance(string $className, array $parameters = []): object;

}
