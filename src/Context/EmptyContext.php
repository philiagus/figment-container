<?php
/*
 * This file is part of philiagus/figment-container
 *
 * (c) Andreas Eicher <philiagus@philiagus.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Philiagus\Figment\Container\Context;

use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Exception\UndefinedContextException;

readonly class EmptyContext implements Context
{

    #[\Override]
    public function has(string $name): bool
    {
        return false;
    }

    #[\Override]
    public function get(string $name): mixed
    {
        throw new UndefinedContextException($name);
    }
}
