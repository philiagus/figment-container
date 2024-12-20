<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

interface OverwriteConstructorParameterProvider
{
    public function resolveOverwriteConstructorParameter(string $forName): array;
}
