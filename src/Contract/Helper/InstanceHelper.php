<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Helper;

use Philiagus\Figment\Container\Contract\Builder\OverwriteConstructorParameterProvider;
use Philiagus\Figment\Container\Contract\PrependMessageThrowableInterface;
use Philiagus\Figment\Container\Enum\SingletonMode;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Philiagus\Figment\Container\Exception\NotFoundException;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @internal
 */
interface InstanceHelper
{
    /**
     * Returns the singleton mode set for the class by its attributes
     * If no such attribute exists, the singleton mode "BY_BUILDER" is implied
     *
     * @return SingletonMode
     * @see SingletonMode
     */
    public function getSingletonMode(): SingletonMode;

    /**
     * @param OverwriteConstructorParameterProvider $builder
     * @param string $id
     *
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PrependMessageThrowableInterface
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function buildInjected(OverwriteConstructorParameterProvider $builder, string $id): object;

    /**
     * @param OverwriteConstructorParameterProvider $parameterProvider
     * @param string $id
     *
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PrependMessageThrowableInterface
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function buildConstructed(OverwriteConstructorParameterProvider $parameterProvider, string $id): object;
}
