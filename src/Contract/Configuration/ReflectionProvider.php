<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Configuration;

interface ReflectionProvider
{
    public function get(string $className): ClassReflection;
}
