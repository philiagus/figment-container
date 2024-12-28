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

use Philiagus\Figment\Container\Contract\Builder;

interface ListBuilder extends Registrable, Builder
{

    /**
     * Appends the defined builders to the end of the list
     * The builders can be provided as instances, created by the configuration
     * and/or as ids
     *
     * @param Builder|string ...$builder
     *
     * @return $this
     */
    public function append(Builder|string ...$builder): static;

    /**
     * Merges the provided builder into the current builder
     * This enables appending the contents of a list to another list
     *
     * The lists are lazily evaluated, so any alteration happening to the list
     * after this point will be reflected in the result of the builder.
     *
     * If you want to append an existing list to the current list while eagerly
     * evaluating its content you can use ->append(...$theOtherList)
     *
     * @param Builder ...$builder
     *
     * @return $this
     * @see self::append()
     */
    public function merge(Builder ...$builder): static;

}
