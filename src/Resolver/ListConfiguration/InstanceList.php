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

namespace Philiagus\Figment\Container\Resolver\ListConfiguration;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Resolver\Proxy\TypedInstanceProxy;
use Traversable;

readonly class InstanceList implements Contract\List\InstanceList, \IteratorAggregate
{

    private array $resolvers;

    /**
     * @param Contract\Resolver ...$resolvers
     */
    public function __construct(Contract\Resolver ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function traverseResolvers(null|\Closure|string|array $type = null): \Generator
    {
        if($type) {
            foreach($this->resolvers as $resolver) {
                yield new TypedInstanceProxy($resolver, $type);
            }
        } else {
            yield from $this->resolvers;
        }
    }

    public function traverseInstances(null|\Closure|string|array $type = null): \Generator
    {
        foreach($this->traverseResolvers($type) as $resolver) {
            yield $resolver->resolve();
        }
    }

    public function getIterator(): Traversable
    {
        yield from $this->traverseInstances();
    }

    public function count(): int
    {
        return count($this->resolvers);
    }
}
