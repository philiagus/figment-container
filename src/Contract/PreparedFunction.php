<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract;

interface PreparedFunction
{

    public function invoke(mixed ...$arguments): mixed;

    public function __invoke(mixed ...$arguments): mixed;
}
