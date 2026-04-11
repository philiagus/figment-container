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

use Philiagus\Figment\Container\Builder\Proxy\TypeCheckBuilderProxy;
use Philiagus\Figment\Container\Contract\Builder;
use Philiagus\Figment\Container\Contract\PrependMessageThrowableInterface;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Philiagus\Figment\Container\Exception\NotFoundException;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @internal
 */
readonly final class InstanceMap implements Contract\InstanceMap, \IteratorAggregate
{

    /**
     * @param string $id
     * @param array $keys
     * @param array<string|Builder> $builders
     */
    public function __construct(
        private string $id,
        private array $keys,
        private array $builders
    )
    {
        if(
            count($keys) !== count($this->builders) ||
            !array_is_list($keys) ||
            !array_is_list($builders)
        ) {
            throw new \InvalidArgumentException(
                '$keys and $builders of InstanceMap must be provided as mapping arrays'
            );
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PrependMessageThrowableInterface
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function getIterator(): \Traversable
    {
        yield from $this->traverseInstances();
    }

    /** @inheritDoc */
    public function traverseInstances(null|\Closure|string|array $type = null): \Traversable
    {
        foreach($this->keys as $index => $key) {
            yield $key => $this->builders[$index]->build("$this->id#$index");
        }
    }

    /** @inheritDoc */
    public function traverseBuilders(null|\Closure|string|array $type = null): \Traversable
    {
        if ($type) {
            foreach ($this->keys as $builder) {
                yield new TypeCheckBuilderProxy($builder, $type);
            }
        } else {
            yield from $this->builders;
        }
    }

    /** @inheritDoc */
    public function count(): int
    {
        return count($this->builders);
    }

    /** @inheritDoc */
    public function has(mixed $key): bool
    {
        return in_array($key, $this->keys, true);
    }

    /** @inheritDoc */
    public function keys(): array
    {
        return $this->keys;
    }

    /** @inheritDoc */
    public function getInstance(mixed $key, null|\Closure|string|array $type = null): object
    {
        $index = $this->getIndex($key);
        return $this->getBuilder($key, $type)->build("$this->id#$index");
    }

    private function getIndex(mixed $key): int
    {
        $index = array_search($key, $this->keys, true);
        if($index === false) {
            throw new ContainerException("Accessing out of bounds key of InstanceMap '$this->id'");
        }

        return $index;
    }

    /** @inheritDoc */
    public function getBuilder(mixed $key, null|\Closure|string|array $type = null): Contract\Builder
    {
        $index = $this->getIndex($key);
        if($type !== null) {
            return new TypeCheckBuilderProxy($this->builders[$index], $type);
        }
        return $this->builders[$index];
    }
}
