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

namespace Philiagus\Figment\Container\Contract;


interface InstanceList extends \Traversable, \Countable
{


    /**
     * Iterates through the builders
     *
     * @template TResult as object
     *
     * @param null|\Closure|class-string<TResult>|class-string[] $type
     *
     * @return \Iterator<int, Builder<TResult>>
     */
    public function traverseBuilders(null|\Closure|string|array $type = null): \Traversable;

    /**
     * Traverses through the resolved instances of this list
     * Every iteration must call the resolver again, leaving singleton handling to
     * the instances
     *
     * This must also be the default \Traversable when iterating this object itself
     * with $type = null
     *
     * @template TResult as object
     * @param null|\Closure|class-string<TResult>|class-string[] $type
     * @return \Iterator<int, TResult>
     */
    public function traverseInstances(null|\Closure|string|array $type = null): \Traversable;

}
