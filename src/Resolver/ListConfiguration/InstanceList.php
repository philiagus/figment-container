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

    public function traverseResolvers(): \Generator
    {
        yield from $this->resolvers;
    }

    public function traverseInstances(): \Generator
    {
        foreach($this->resolvers as $resolver) {
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
