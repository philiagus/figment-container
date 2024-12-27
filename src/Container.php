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

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Contract\Context;

readonly final class Container implements Contract\Container
{

    public function __construct(private Contract\BuilderContainer $provider)
    {
    }

    /** @inheritDoc */
    public function get(string $id): object
    {
        return $this->provider->get($id)->build($id);
    }

    /** @inheritDoc */
    public function has(string $id): bool
    {
        return $this->provider->has($id);
    }

    public function context(): Context
    {
        return $this->provider->context();
    }
}
