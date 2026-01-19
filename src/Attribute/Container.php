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

use Philiagus\Figment\Container\Contract;


/**
 * Use this to inject the Container into the specified parameter
 *
 * @see Configuration::attributed()
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class Container implements Contract\InjectionAttribute
{

    /** @inheritDoc */
    #[\Override]
    public function resolve(
        Contract\Container $container,
        \ReflectionParameter $parameter,
        string $id,
        false &$hasValue
    ): ?object
    {
        $hasValue = true;
        return $container;
    }
}
