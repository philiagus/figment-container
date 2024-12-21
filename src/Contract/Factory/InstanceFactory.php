<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Factory;

use Philiagus\Figment\Container\Contract\Builder\OverwriteConstructorParameterProvider;
use Philiagus\Figment\Container\Contract;

interface InstanceFactory
{
    public function buildInjected(Contract\Container&OverwriteConstructorParameterProvider $provider, string $forName): object;

    public function buildConstructed(OverwriteConstructorParameterProvider $parameterProvider, string $forName): object;
}
