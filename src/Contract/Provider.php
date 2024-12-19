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

interface Provider
{

    /**
     * @param string $id
     * @return Resolver
     */
    public function get(string $id): Resolver;

    /**
     * Returns true if this provider can provide
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * @return Context
     */
    public function context(): Context;
}
