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
use Philiagus\Figment\Container\Contract\Override\OverridableContext;
use Philiagus\Figment\Container\Contract\Override\OverridableContainer;
use Philiagus\Figment\Container\Contract\BuilderContainer;

interface InjectionBuilder extends Registrable, Builder, OverridableContainer, OverwriteConstructorParameterReceiver
{
}
