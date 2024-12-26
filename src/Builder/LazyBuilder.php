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

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\NotFoundException;

/**
 * Used to lazily resolve targeted ids
 * This is used by the framework to resolve get requests to the configuration
 * for not-already exposed services - something without which recursive dependencies
 * are not possible
 *
 * @internal
 */
readonly class LazyBuilder implements Contract\Builder, \IteratorAggregate
{

    private Contract\Builder $builder;

    /**
     * @param Contract\Configuration $configuration
     * @param string $id
     */
    public function __construct(
        private Contract\Configuration $configuration,
        private string                 $id
    )
    {
    }

    public function build(string $name): object
    {
        return $this->evaluate()->build($name);
    }

    public function evaluate(): Contract\Builder
    {
        if (!isset($this->builder)) {
            if($this->configuration->has($this->id)) {
                $this->builder = $this->configuration->get($this->id);
            } else {
                if (!class_exists($this->id)) {
                    throw new NotFoundException($this->id);
                }
                $builder = $this->configuration->injected($this->id);
                $builder->registerAs($this->id);
                $this->builder = $builder;
            }
        }

        return $this->builder;
    }

    /**
     * Returns an iterator that iterates over the Resolvable instances
     * that make up the targeted resolvable. This only contains more than one
     * element if the target is a list.
     *
     * This is used to lazy-expand lists to their contained Resolvable elements.
     *
     * @return \Traversable<int, Contract\Builder>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->evaluate();
    }
}
