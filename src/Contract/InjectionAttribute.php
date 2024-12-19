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
use ReflectionProperty;

interface InjectionAttribute
{
    public function resolve(Provider $provider, ReflectionProperty|ReflectionParameter $target, bool &$hasValue): mixed;
}
