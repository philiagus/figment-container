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
use Philiagus\Figment\Container\Contract\Exposable;
use Philiagus\Figment\Container\Contract\Injector;
use Philiagus\Figment\Container\Contract\Resolvable;

/**
 * Classes implementing this interface can be influenced in the way that they
 * instantiate objects
 */
interface InstanceConfigurator extends Exposable
{

    /**
     * Configures to redirect the targeted to another instance when requesting it via
     * Injector::inject.
     *
     * @param string $from
     * @param Resolvable $to
     *
     * @return $this
     * @see Injector::inject()
     */
    public function redirect(string $from, Resolvable $to): self;

    /**
     * Defines the provided array or context provider as the context for this instance.
     *
     * @param array|Provider $context
     * @param bool $fallbackToDefault
     * @return $this
     * @see Injector::context()
     * @see Injector::parseContext()
     */
    public function context(array|Provider $context, bool $fallbackToDefault = false): self;

}
