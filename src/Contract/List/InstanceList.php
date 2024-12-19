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

namespace Philiagus\Figment\Container\Contract\List;

use Philiagus\Figment\Container\Contract\Resolver;

/**
 * @template-covariant Content
 * @implements \Traversable<int, Content>
 */
interface InstanceList extends \Traversable, \Countable {


    /**
     * Iterates through the resolvers
     * @param null|\Closure|class-string|class-string[] $type
     * @return \Generator<int, Resolver<Content>>
     */
    public function traverseResolvers(null|\Closure|string|array $type = null): \Generator;

    /**
     * Traverses through the resolved instances of this list
     * Every iteration must call the resolver again, leaving singleton handling to
     * the instances
     *
     * This must also be the default \Traversable when iterating this object itself
     * @param null|\Closure|class-string|class-string[] $type
     * @return \Generator<int, Content>
     */
    public function traverseInstances(null|\Closure|string|array $type = null): \Generator;

}
