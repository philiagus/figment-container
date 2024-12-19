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
use ReflectionParameter;
use ReflectionProperty;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class InjectList implements Contract\InjectionAttribute
{

    public function __construct(
        private string $id,
        private bool   $emptyIfNotExists = false
    )
    {
    }

    public function resolve(Contract\Provider $provider, ReflectionProperty|ReflectionParameter $target, bool &$hasValue): mixed
    {
        if ($this->emptyIfNotExists && !$provider->has($this->id)) {
            $list = new InstanceList();
        } else {
            $resolved = $provider->get($this->id)->resolve();
            if (!$resolved instanceof Contract\List\InstanceList) {
                throw new ContainerException("{$this->id} did not resolve to a list");
            }

            $list = $resolved;
        }
        $hasValue = true;
        return $list;
    }
}
