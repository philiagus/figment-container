<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Configuration;

interface OverwriteConstructorParameterProvider
{
    public function resolveOverwriteConstructorParameter(): array;
}
