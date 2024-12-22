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
     * @param bool $hasValue Will be set to true if the parameter has a value
     *
     * @return mixed
     */
    public function resolve(
        Container            $container,
        \ReflectionParameter $parameter,
        false                &$hasValue
    ): mixed;
}
