<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Override;

use Philiagus\Figment\Container\Contract\Builder;
use Philiagus\Figment\Container\Contract\Container;

interface OverridableContainer extends OverridableContext, Container {

    public function redirect(string $id, Builder|string $to): static;

}
