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

namespace Philiagus\Figment\Container\List;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Instance\InstanceResolver;
use Philiagus\Figment\Container\Contract\List\ListResolver;

class ListConfigurator implements Contract\List\ListConfigurator
{

    /**
     * @param \Closure $listExposer
     * @param array<InstanceResolver|ListResolver> $content
     */
    public function __construct(
        private readonly \Closure $listExposer,
        private array             $content = []
    )
    {
        $this->content = array_values($this->content);
    }

    public function exposeAs(string $name): Contract\List\ListExposer
    {
        ($this->listExposer)($name);

        return $this;
    }

    public function append(InstanceResolver ...$instance): Contract\List\ListConfigurator
    {
        $this->content = [...$this->content, ...array_values($instance)];

        return $this;
    }

    public function concat(ListResolver ...$list): Contract\List\ListConfigurator
    {
        $this->content = [...$this->content, ...array_values($list)];

        return $this;
    }

    public function resolve(): Contract\List\InstanceList
    {
        $instanceResolvers = [];
        foreach ($this->content as $index => $item) {
            if ($item instanceof InstanceResolver) {
                $instanceResolvers[$index] = $item;
            } else {
                $instanceResolvers = [...$instanceResolvers, ...$item->resolve()->resolvers()];
            }
        }
        return new InstanceList($instanceResolvers);
    }
}
