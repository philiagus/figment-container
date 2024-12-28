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
use Philiagus\Figment\Container\Contract\PrependMessageThrowableInterface;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Philiagus\Figment\Container\Exception\NotFoundException;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

readonly final class InstanceList implements Contract\InstanceList, \IteratorAggregate
{

    private array $builders;

    /**
     * @param Contract\Builder ...$builders
     */
    public function __construct(
        private string $name,
        Contract\Builder ...$builders
    )
    {
        $this->builders = $builders;
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
        foreach ($this->traverseBuilders($type) as $index => $builder) {
            yield $builder->build("$this->name#$index");
        }
    }

    /** @inheritDoc */
    public function traverseBuilders(null|\Closure|string|array $type = null): \Traversable
    {
        if ($type) {
            foreach ($this->builders as $builder) {
                yield new TypeCheckProxy($builder, $type);
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
}
