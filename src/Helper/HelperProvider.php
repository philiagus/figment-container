<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Helper;

use Philiagus\Figment\Container\Contract;

/**
 * @internal
 */
class HelperProvider implements Contract\Helper\HelperProvider
{

    /** @var array<string, Contract\Helper\InstanceHelper> */
    private array $cache = [];

    /** @inheritDoc */
    #[\Override]
    public function get(string $className): InstanceHelper
    {
        return $this->cache[$className] ??= new InstanceHelper($className);
    }
}
