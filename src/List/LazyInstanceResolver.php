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

namespace Philiagus\Figment\Container\List;

use Philiagus\Figment\Container\Contract\Instance\InstanceResolver;

readonly class LazyInstanceResolver implements InstanceResolver
{
    public function __construct(private \Closure $fire)
    {
    }

    public function resolve(bool $disableSingleton = false): object
    {
        return ($this->fire)($disableSingleton);
    }
}
