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

namespace Philiagus\Figment\Container\Contract\Configuration;

use Philiagus\Figment\Container\Contract\Resolver;

interface ListConfigurator extends Registrable, Resolver {

    public function append(Resolver|string ...$resolver): self;

    public function merge(Resolver $listConfigurator): self;

}
