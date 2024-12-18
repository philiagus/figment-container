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

readonly class TypedInstanceList implements Contract\List\InstanceList, \IteratorAggregate
{

    public function __construct(
        private Contract\List\InstanceList $parentList,
        private string|\Closure $type
    )
    {

    }

    public function getIterator(): \Traversable
    {
        yield from $this->traverseInstances();
    }

    public function traverseInstances(): \Generator
    {
        foreach($this->traverseResolvers() as $resolver) {
            yield $resolver->resolve();
        }
    }

    public function count(): int
    {
        return $this->parentList->count();
    }

    public function traverseResolvers(): \Generator
    {
        foreach($this->parentList->traverseResolvers() as $resolver) {
            yield new TypedInstanceProxy($resolver, $this->type);
        }
    }
}
