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

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Contract\Instance\InstanceResolver;
use Philiagus\Figment\Container\Contract\List\ListResolver;

readonly class Container implements Contract\Container
{

    public function __construct(
        private Contract\Configuration $configuration,
        ?string                        $exposeAs = null
    )
    {
        if ($exposeAs !== null)
            $this->configuration
                ->instanceObject($this)
                ->exposeAs($exposeAs);
    }

    public function list(string $name): Contract\List\InstanceList
    {
        return $this->listResolver($name)->resolve();
    }

    public function listResolver(string $name): ListResolver
    {
        return $this->configuration->listResolver($name);
    }

    public function instance(string $name, bool $disableSingleton = false): object
    {
        return $this->instanceResolver($name)->resolve($disableSingleton);
    }

    public function instanceResolver(string $name): InstanceResolver
    {
        return $this->configuration->instanceResolver($name);
    }
}
