<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Configuration;

use Philiagus\Figment\Container\Contract\Context;

interface OverridableContext
{
    public function setContext(Context|array $context, bool $enableFallback = false): static;
}
