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

namespace Philiagus\Figment\Container\Attribute;

use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Contract\InjectionAttribute;

/**
 * Will inject the current id of the builder into the created instance as string
 * Please be aware that any class that is not singleton disabled will only be
 * instantiated once, even if it is registered under multiple ids so you cannot
 * relly on the created instance only being used under the received id.
 *
 * Singletons are handled on builder level. If you want to ensure a class is
 * only used on per-id basis please either disable singleton or set it to the
 * appropriate singleton mode
 *
 * @see Singleton
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class ContainerId implements InjectionAttribute
{

    public function __construct()
    {
    }

    public function resolve(
        Container $container,
        \ReflectionParameter $parameter,
        string $id,
        false &$hasValue
    ): string
    {
        $hasValue = true;
        return $id;
    }
}
