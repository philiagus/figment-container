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

namespace Philiagus\Figment\Container\Contract\Builder;

interface Registrable
{

    /**
     * Registers the builder at the configuration under the provided ids
     *
     * Registering builders allows the injection to use them and the container
     * to instantiate them under the defined ids
     *
     * @param non-empty-string ...$id
     *
     * @return Registrable
     */
    public function registerAs(string ...$id): Registrable;

}
