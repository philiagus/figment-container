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

use Closure;
use Philiagus\Figment\Container\Contract\List\InstanceList;
use Philiagus\Figment\Container\Contract\List\ListResolver;

readonly class LazyListResolver implements ListResolver
{

    /**
     * @param Closure $fire
     */
    public function __construct(
        private Closure $fire
    )
    {
    }

    public function resolve(): InstanceList
    {
        return ($this->fire)();
    }
}
