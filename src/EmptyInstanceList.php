<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container;

readonly final class EmptyInstanceList implements Contract\InstanceList, \IteratorAggregate
{

    public function count(): int
    {
        return 0;
    }

    public function traverseBuilders(array|string|\Closure|null $type = null): \Traversable
    {
        return new \EmptyIterator();
    }

    public function traverseInstances(array|string|\Closure|null $type = null): \Traversable
    {
        return new \EmptyIterator();
    }

    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
