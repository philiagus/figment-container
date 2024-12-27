<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Helper;

use Philiagus\Figment\Container\Contract\Builder\OverwriteConstructorParameterProvider;
use Philiagus\Figment\Container\Contract\ContainerTraceException;
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
     * @param OverwriteConstructorParameterProvider $builder
     * @param string $id
     *
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ContainerTraceException
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
     * @throws ContainerTraceException
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function buildConstructed(OverwriteConstructorParameterProvider $parameterProvider, string $id): object;
}
