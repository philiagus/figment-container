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

use Philiagus\Figment\Container\Builder\Proxy\TypeCheckProxy;

readonly class InstanceList implements Contract\InstanceList, \IteratorAggregate
{

    private array $resolvers;

    /**
     * @param Contract\Builder ...$resolvers
     */
    public function __construct(
        private string $name,
        Contract\Builder ...$resolvers
    )
    {
        $this->resolvers = $resolvers;
    }

    public function traverseBuilders(null|\Closure|string|array $type = null): \Generator
    {
        if($type) {
            foreach($this->resolvers as $resolver) {
                yield new TypeCheckProxy($resolver, $type);
            }
        } else {
            yield from $this->resolvers;
        }
    }

    public function traverseInstances(null|\Closure|string|array $type = null): \Generator
    {
        foreach($this->traverseBuilders($type) as $index => $resolver) {
            yield $resolver->build("{$this->name}#$index");
        }
    }

    public function getIterator(): \Traversable
    {
        yield from $this->traverseInstances();
    }

    public function count(): int
    {
        return count($this->resolvers);
    }
}
