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

readonly class InstanceList implements Contract\List\InstanceList
{

    /**
     * @param InstanceResolver[] $instanceResolvers
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

    public function offsetGet(mixed $offset): Contract\Instance\InstanceResolver
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

    public function getIterator(bool $disableSingleton = false): \Traversable
    {
        foreach ($this->instanceResolvers as $index => $instanceResolver)
            yield $index => $instanceResolver->resolve($disableSingleton);
    }
}
