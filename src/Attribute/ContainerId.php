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
use Philiagus\Figment\Container\Enum\SingletonMode;

/**
 * Will inject the current id of the builder into the created instance as string
 * IMPORTANT: Depending on the singleton mode a class might only be
 * instantiated once (even if registered under multiple ids).
 *
 * Singletons are handled on builder level. If you want to ensure a class is
 * only used on per-id basis please either disable singleton or set it to the
 * appropriate singleton mode
 *
 * @see Singleton
 * @see SingletonMode
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class ContainerId implements InjectionAttribute
{

    public function __construct()
    {
    }

    /** @inheritDoc */
    #[\Override]
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
