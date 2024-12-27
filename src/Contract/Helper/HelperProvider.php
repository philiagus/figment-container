<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Helper;

use Philiagus\Figment\Container\Exception\ContainerException;

/**
 * @internal
 */
interface HelperProvider
{
    /**
     * @param string $className
     *
     * @return InstanceHelper
     * @throws ContainerException
     */
    public function get(string $className): InstanceHelper;
}
