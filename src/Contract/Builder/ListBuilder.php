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

interface ListBuilder extends Registrable, Builder {

    public function append(Builder|string ...$resolver): static;

    public function merge(Builder $listConfigurator): static;

}
