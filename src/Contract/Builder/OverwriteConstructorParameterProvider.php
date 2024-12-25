<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Contract\BuilderContainer;

interface OverwriteConstructorParameterProvider extends BuilderContainer
{
    public function resolveOverwriteConstructorParameter(string $forName): array;
}
