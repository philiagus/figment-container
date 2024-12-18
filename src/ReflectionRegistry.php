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

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Contract\InjectionAttribute;

class ReflectionRegistry
{

    /** @var array<string, ReflectionRegistry\ClassReflection> */
    private static array $cache = [];

    public static function getClassReflection(string $class): ReflectionRegistry\ClassReflection
    {
        return self::$cache[$class] ??= new ReflectionRegistry\ClassReflection($class);
    }

}
