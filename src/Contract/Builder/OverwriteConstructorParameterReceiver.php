<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Contract\Builder;

interface OverwriteConstructorParameterReceiver
{
    public function parameterSet(string $name, mixed $value): static;
    public function parameterInject(string $name, string|Builder $injection): static;
    public function parameterConfig(string $name): static;
}
