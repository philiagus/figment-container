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

use Philiagus\Figment\Container\Exception\UndefinedContextException;

interface Context
{
    /**
     * Returns true if the target name is defined within this Context
     * Please be aware that a value of NULL still is considered set!
     * Think of it as "Does this path exist within the context", ignoring
     * what value this path contains.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Returns the value of the context as defined by the name
     * @param string $name
     * @return mixed
     * @throws UndefinedContextException
     */
    public function get(string $name): mixed;
}

