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

use Philiagus\Figment\Container\Contract\Injectable;

/**
 * Used to lazily resolve targeted ids
 * This is used by the framework to resolve get requests to the configuration
 * for not-already exposed services - something without which recursive dependencies
 * are not possible
 *
 * @internal
 */
readonly class LazyResolvable implements Contract\Resolvable, \IteratorAggregate
{

    private Contract\Resolvable $resolvable;

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

    public function resolve(): object
    {
        return $this->evaluate()->resolve();
    }

    private function evaluate(): Contract\Resolvable
    {
        if (!isset($this->resolvable)) {
            $resolvable = $this->configuration->get($this->id);
            if ($resolvable instanceof LazyResolvable) {
                /*
                The container still returns a lazy resolvable, so
                the targeted id has not been exposed against yet
                If the call is targeting a class we can register, expose and resolve that
                In any other case we throw a NotFoundException, as this cannot be resolved
                */
                if(!is_a($this->id, Injectable::class, true)) {
                    throw new NotFoundException($this->id);
                }
                $resolvable = $this->configuration->class($this->id);
                $resolvable->exposeAs($this->id);
            }
            $this->resolvable = $resolvable;
        }

        return $this->resolvable;
    }

    /**
     * Returns an iterator that iterates over the Resolvable instances
     * that make up the targeted resolvable. This only contains more than one
     * element if the target is a list.
     *
     * This is used to lazy-expand lists to their contained Resolvable elements.
     *
     * @return \Traversable<int, Contract\Resolvable>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->evaluate();
    }
}
