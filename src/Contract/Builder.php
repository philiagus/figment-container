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

/**
 * @template-covariant TResult as object
 */
interface Builder extends \Traversable
{
    /**
     * Builds the targeted instance
     * Name is a helpful identifier to help the consumer of any exception know which chain
     * of instantiation might have caused any exception
     * @param string $name
     * @return TResult
     */
    public function build(string $name): object;
}
