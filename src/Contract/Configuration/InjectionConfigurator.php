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

use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Contract\Provider;
use Philiagus\Figment\Container\Contract\Resolver;

interface InjectionConfigurator extends Registrable, Resolver, Provider, OverwriteConstructorParameterReceiver, OverridableContext
{
    public function redirect(string $id, Resolver|string $resolver): static;
}
