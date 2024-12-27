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
use ReflectionParameter;

/**
 * @internal
 */
interface InjectionAttribute
{
    /**
     * Resolves the injection attribute, using the container and hints from the
     * parameter information
     *
     * @param Container $container
     * @param ReflectionParameter $parameter
     * @param string $id
     * @param bool $hasValue Will be set to true if the parameter has a value
     *
     * @return mixed
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ContainerTraceException
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function resolve(
        Container            $container,
        \ReflectionParameter $parameter,
        string               $id,
        false                &$hasValue
    ): mixed;
}
