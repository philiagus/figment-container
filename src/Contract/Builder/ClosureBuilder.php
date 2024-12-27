<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Contract;

interface ClosureBuilder
    extends Contract\Builder, Registrable, OverwriteSingletonMode
{
}
