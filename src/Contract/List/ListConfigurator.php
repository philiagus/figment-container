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
 * Implementing classes are defined as list configuration classes
 */
interface ListConfigurator extends ListResolver, ListExposer
{

    /**
     * Add the specified list of resolvers to the end of the list.
     * This method will ignore named parameters, so setting names for the instances
     * is not possible using this method.
     *
     * @param InstanceResolver ...$instance
     * @return self
     */
    public function append(InstanceResolver ...$instance): self;

    /**
     * Concat the provided lists to this list.
     * The lists are expanded on resolve, so any changes made to the provided lists
     * after calling concat will alter the result of this configurator
     *
     * Any keys provided by those lists will be ignored. The elements of the lists will
     * be added to the sequence of instances provided by the list
     *
     * @param ListResolver ...$list
     * @return self
     */
    public function concat(ListResolver ...$list): self;
}
