<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Override;

use Philiagus\Figment\Container\Contract\Context;

interface OverridableContext
{
    public function setContext(Context $context, bool $enableFallback = false): static;

    public function context(): Context;
}
