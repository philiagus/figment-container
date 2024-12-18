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

use Philiagus\Figment\Container\ContainerException;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Resolver\ListConfiguration\InstanceList;
use Philiagus\Figment\Container\Resolver\ListConfiguration\TypedInstanceList;
use ReflectionProperty;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class InjectList implements Contract\InjectionAttribute
{

    public function __construct(
        private string  $id,
        private null|string|array|\Closure $typeCheck = null,
        private bool    $emptyIfNotExists = false
    )
    {
    }

    public function resolve(Contract\Provider $provider, ReflectionProperty $property, object $object): void
    {
        if ($this->emptyIfNotExists && !$provider->has($this->id)) {
            $list = new InstanceList();
        } else {
            $resolved = $provider->get($this->id)->resolve();
            if (!$resolved instanceof Contract\List\InstanceList) {
                throw new ContainerException("{$this->id} did not resolve to a list");
            }

            $list = $this->typeCheck === null ?
                $resolved :
                new TypedInstanceList($resolved, $this->typeCheck);
        }
        $property->setValue($object, $list);
    }
}
