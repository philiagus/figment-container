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

readonly class Container implements Contract\Container
{

    public function __construct(
        private Contract\Configuration $configuration,
        ?string                        $exposeAs = null
    )
    {
        if ($exposeAs !== null)
            $this->configuration
                ->object($this)
                ->exposeAs($exposeAs);
    }

    /** @inheritDoc */
    public function get(string $id)
    {
        return $this->configuration->get($id)->resolve();
    }

    /** @inheritDoc */
    public function has(string $id): bool
    {
        return $this->configuration->has($id);
    }
}
