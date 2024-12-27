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

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\UndefinedContextException;

readonly class MapContext implements Contract\Context
{

    public function __construct(private array $context)
    {
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->context);
    }

    public function get(string $name): mixed
    {
        return $this->context[$name] ?? throw new UndefinedContextException($name);
    }
}
