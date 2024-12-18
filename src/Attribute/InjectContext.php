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
use ReflectionProperty;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class InjectContext implements InjectionAttribute {

    public function __construct(
        private string $name
    ) {}

    public function resolve(Provider $provider, ReflectionProperty $property, object $object): void
    {
        $property->setValue(
            $object, $provider->context()->get($this->name)
        );
    }
}
