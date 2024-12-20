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

interface InjectionAttribute
{
    /**
     * @param Container $container
     * @param \ReflectionParameter $parameter
     * @param bool $hasValue
     * @return mixed
     */
    public function resolve(Container $container, \ReflectionParameter $parameter, bool &$hasValue): mixed;
}
