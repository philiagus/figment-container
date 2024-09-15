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

use Psr\Container\ContainerInterface;

/**
 * Base interface of any container created by the framework.
 *
 * A container usually wraps a container configuration and allows access to
 * its contents using the resolve methods
 */
interface Container extends ContainerInterface
{
}
