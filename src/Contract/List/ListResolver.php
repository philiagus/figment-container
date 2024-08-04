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

/**
 * Implementing classes can resolve themselves to a InstanceList
 */
interface ListResolver
{
    /**
     * Resolves the list and provides the InstanceList object containing
     * the instance resolvers.
     *
     * The resolved list is always flat - any concatenated lists are
     * resolved as well and ordered at the moment of resolve
     *
     * @return InstanceList
     */
    public function resolve(): InstanceList;
}
