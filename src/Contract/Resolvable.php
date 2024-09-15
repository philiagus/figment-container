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

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Implementing classes are able to create objects based in their configuration
 */
interface Resolvable extends \Traversable
{
    /**
     * Trigger this class to resolve its content and making available an instance
     * of the class as defined.
     *
     *                               new object instead of returning a
     *                               possibly already existing one
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function resolve(): object;
}
