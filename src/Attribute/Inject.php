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
use Philiagus\Figment\Container\Resolver\Proxy\TypedInstanceProxy;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class Inject implements InjectionAttribute
{

    public function __construct(
        private string                     $id,
        private null|string|array|\Closure $typeCheck = null
    )
    {
    }

    public function resolve(Provider $provider, \ReflectionProperty $property, object $object): void
    {
        $resolver = $provider->get($this->id);
        if ($this->typeCheck !== null) {
            $instance = new TypedInstanceProxy($resolver, $this->typeCheck)->resolve();
        } else {
            $instance = $resolver->resolve();
        }

        $property->setValue($object, $instance);
    }
}
