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

use Philiagus\Figment\Container\Contract\Instance\InstanceResolver;

/**
 * When requesting a list from the container the system will expose
 * a class implementing this interface.
 *
 * Instances of lists are usually evaluated lazy. Looping through the iterator
 * or accessing via array offset will always yield a resolved instance.
 *
 * The resolvers are accessible using the getResolvers method
 */
interface InstanceList extends \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Returns the raw list of resolvers from the instance list
     * @return InstanceResolver[]
     */
    public function resolvers(): array;

    /**
     * @inheritDoc
     */
    public function getIterator(bool $disableSingleton = false): \Traversable;
}
