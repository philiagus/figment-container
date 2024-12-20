<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Contract\Builder;

interface ConstructorBuilder extends Builder, Registrable, OverwriteConstructorParameterReceiver
{
    public function disableSingleton(): self;
}
