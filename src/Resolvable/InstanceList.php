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

namespace Philiagus\Figment\Container\Resolvable;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Resolvable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @internal
 */
readonly class InstanceList implements Contract\List\InstanceList, \IteratorAggregate
{

    /**
     * @param Resolvable[] $instanceResolvers
     */
    public function __construct(
        private array $instanceResolvers
    )
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->instanceResolvers[$offset]);
    }

    public function offsetGet(mixed $offset): Contract\Resolvable
    {
        return $this->instanceResolvers[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException("Offsets in ListResolverResult cannot be set");
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException("Offsets in ListResolverResult cannot be unset");
    }

    public function count(): int
    {
        return count($this->instanceResolvers);
    }

    /** @inheritDoc */
    public function resolvers(): array
    {
        return $this->instanceResolvers;
    }

    /**
     * Iterates over the instances of this list
     *
     * Each iteration will call the resolve method of the list contents,
     * this means that any re-iteration over this iterator that targets
     * classes that have singleton disabled are re-instantiated in every
     * loop
     *
     * @return \Traversable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->instanceResolvers as $index => $instanceResolver)
            yield $index => $instanceResolver->resolve();
    }
}
