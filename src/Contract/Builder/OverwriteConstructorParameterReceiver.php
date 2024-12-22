<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Contract;

interface OverwriteConstructorParameterReceiver
{
    public function parameterSet(string $name, mixed $value): static;

    public function parameterInject(string $name, string|Contract\Builder $injection): static;

    public function parameterContext(string $name, string $context): static;

    /**
     * @param string $name
     * @param \Closure(Contract\Container $container, string $forName): mixed $generator
     *
     * @return $this
     */
    public function parameterGenerate(string $name, \Closure $generator): static;
}
