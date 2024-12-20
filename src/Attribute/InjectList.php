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
use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\InstanceList;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class InjectList implements Contract\InjectionAttribute
{

    public function __construct(
        private string $id,
        private bool   $emptyIfNotExists = false
    )
    {
    }

    public function resolve(Container $container, \ReflectionParameter $parameter, bool &$hasValue): mixed
    {
        if ($this->emptyIfNotExists && !$container->has($this->id)) {
            $hasValue = true;
            return new InstanceList('');
        }
        $resolved = $container->get($this->id);
        if (!$resolved instanceof Contract\InstanceList) {
            throw new ContainerException("{$this->id} did not resolve to a list");
        }

        $hasValue = true;
        return $resolved;
    }
}
