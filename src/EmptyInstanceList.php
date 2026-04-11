<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container;

readonly final class EmptyInstanceList implements Contract\InstanceList, \IteratorAggregate
{

    /** @inheritDoc */
    #[\Override]
    public function count(): int
    {
        return 0;
    }

    /** @inheritDoc */
    #[\Override]
    public function traverseBuilders(array|string|\Closure|null $type = null): \Traversable
    {
        return new \EmptyIterator();
    }

    /** @inheritDoc */
    #[\Override]
    public function traverseInstances(array|string|\Closure|null $type = null): \Traversable
    {
        return new \EmptyIterator();
    }

    /** @inheritDoc */
    #[\Override]
    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
