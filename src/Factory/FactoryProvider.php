<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Factory;

use Philiagus\Figment\Container\Contract;

class FactoryProvider implements Contract\Factory\FactoryProvider
{

    /** @var array<string, Contract\Factory\InstanceFactory> */
    private array $cache = [];

    public function get(string $className): InstanceFactory
    {
        return $this->cache[$className] ??= new InstanceFactory($className);
    }
}
