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

use Philiagus\Figment\Container\Contract\InjectionAttribute;
use Philiagus\Figment\Container\Contract\Provider;
use ReflectionParameter;
use ReflectionProperty;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class Inject implements InjectionAttribute
{

    public function __construct(
        private ?string $id = null
    )
    {
    }

    public function resolve(Provider $provider, ReflectionProperty|ReflectionParameter $target, bool &$hasValue): mixed
    {
        $id = $this->id ?? (string)$target->getType();
        $instance = $provider->get($id)->resolve();
        $hasValue = true;
        return $instance;
    }
}
