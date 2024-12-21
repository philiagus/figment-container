<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Configuration;

interface FactoryProvider
{
    public function get(string $className): InstanceFactory;
}
