<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Configuration;

use Philiagus\Figment\Container\Contract\Resolver;

interface ConstructedConfigurator extends Resolver, Registrable, OverwriteConstructorParameterReceiver
{
    public function disableSingleton(): self;
}
