<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Helper;

interface HelperProvider
{
    public function get(string $className): InstanceHelper;
}
