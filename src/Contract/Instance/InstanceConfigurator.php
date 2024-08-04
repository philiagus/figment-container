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

namespace Philiagus\Figment\Container\Contract\Instance;

use Philiagus\Figment\Container\Contract\Context\Provider;
use Philiagus\Figment\Container\Contract\Injector;
use Philiagus\Figment\Container\Contract\List\ListResolver;

/**
 * Classes implementing this interface can be influenced in the way that they
 * instantiate objects
 */
interface InstanceConfigurator extends InstanceExposer
{

    /**
     * Configures to redirect the targeted instance to another instance when requesting it via
     * Injector::instance.
     *
     * @param string $from
     * @param InstanceResolver $to
     *
     * @return $this
     * @see Injector::instance()
     */
    public function redirectInstance(string $from, InstanceResolver $to): self;

    /**
     * Configures to redirect the targeted list to another list when requesting it via
     * Injector::list
     *
     * @param string $from
     * @param ListResolver $to
     * @return $this
     * @see Injector::list()
     */
    public function redirectList(string $from, ListResolver $to): self;

    /**
     * Defines the provided array or context provider as the context for this instance.
     *
     * @param array|Provider $context
     * @return $this
     * @see Injector::context()
     * @see Injector::parseContext()
     */
    public function setContext(array|Provider $context): self;

}
