<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Configuration;

use Philiagus\Figment\Container\Contract\Resolver;

interface OverwriteConstructorParameterReceiver
{
    public function parameterSet(string $name, mixed $value): static;
    public function parameterInject(string $name, string|Resolver $injection): static;
    public function parameterConfig(string $name): static;
}
