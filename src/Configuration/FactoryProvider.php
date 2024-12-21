<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Configuration;

use Philiagus\Figment\Container\Contract;

class FactoryProvider implements Contract\Configuration\FactoryProvider
{

    /** @var array<string, Contract\Configuration\InstanceFactory> */
    private array $cache = [];

    public function get(string $className): InstanceFactory
    {
        return $this->cache[$className] ??= new InstanceFactory($className);
    }
}
