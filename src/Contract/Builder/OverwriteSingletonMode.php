<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Enum\SingletonMode;

interface OverwriteSingletonMode
{
    public function singletonMode(SingletonMode $mode): static;
}
