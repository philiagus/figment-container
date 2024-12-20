<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Configuration;

use Philiagus\Figment\Container\Contract;

class ReflectionProvider implements Contract\Configuration\ReflectionProvider
{

    /** @var array<string, Contract\Configuration\ClassReflection> */
    private array $cache =[];

    public function get(string $className): ClassReflection
    {
        return $this->cache[$className] ??= new ClassReflection($className);
    }
}
