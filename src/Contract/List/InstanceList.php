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
 * @template Content
 * @template-implements \Traversable<Content>
 */
interface InstanceList extends \Traversable, \Countable {


    /**
     * Iterates through the resolvers
     * @return \Generator<Resolver<Content>>
     */
    public function traverseResolvers(): \Generator;

    /**
     * Traverses through the resolved instances of this list
     * Every iteration must call the resolver again, leaving singleton handling to
     * the instances
     *
     * This must also be the default \Traversable when iterating this object itself
     * @return \Generator<Content>
     */
    public function traverseInstances(): \Generator;

}
