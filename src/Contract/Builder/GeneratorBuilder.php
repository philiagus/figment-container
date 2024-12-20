<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Contract;

interface GeneratorBuilder extends Contract\Builder, Registrable
{
    public function disableSingleton(): static;
}
