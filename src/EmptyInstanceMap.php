<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Contract\Builder;
use Philiagus\Figment\Container\Exception\ContainerException;
use Traversable;

final readonly class EmptyInstanceMap implements Contract\InstanceMap, \IteratorAggregate
{

    /** @inheritDoc */
    #[\Override]
    public function count(): int
    {
        return 0;
    }

    /** @inheritDoc */
    #[\Override]
    public function traverseBuilders(array|\Closure|string|null $type = null): \Traversable
    {
        return new \EmptyIterator();
    }

    /** @inheritDoc */
    #[\Override]
    public function traverseInstances(array|\Closure|string|null $type = null): \Traversable
    {
        return new \EmptyIterator();
    }

    /** @inheritDoc */
    #[\Override]
    public function has(mixed $key): bool
    {
        return false;
    }

    /** @inheritDoc */
    #[\Override]
    public function keys(): array
    {
        return [];
    }

    /** @inheritDoc */
    #[\Override]
    public function getInstance(mixed $key, array|\Closure|string|null $type = null): object
    {
        throw new ContainerException("Accessing out of bounds key of empty InstanceMap");
    }

    /** @inheritDoc */
    #[\Override]
    public function getBuilder(mixed $key, array|\Closure|string|null $type = null): Builder
    {
        throw new ContainerException("Accessing out of bounds key of empty InstanceMap");
    }

    /** @inheritDoc */
    #[\Override]
    public function getIterator(): Traversable
    {
        return new \EmptyIterator();
    }
}
