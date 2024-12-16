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

namespace Philiagus\Figment\Container\Resolver;

use Closure;
use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Provider;
use Philiagus\Figment\Container\Contract\Registrable;
use Philiagus\Figment\Container\Contract\Resolver;

class InstanceGenerator implements Resolver, Registrable
{
    private object $singleton;
    private bool $running = false;

    /**
     * @param bool $useSingleton
     * @param Configuration $configuration
     * @param Closure(Provider): object $generator
     */
    public function __construct(
        private readonly Configuration $configuration,
        private readonly bool          $useSingleton,
        private readonly \Closure      $generator
    )
    {

    }

    public function resolve(): object
    {
        if (isset($this->singleton)) {
            return $this->singleton;
        }
        if ($this->running) {
            throw new \LogicException(
                "Recursively running creation of instance generator"
            );
        }
        $this->running = true;
        try {
            $result = ($this->generator)($this->configuration);
            if ($this->useSingleton) {
                $this->singleton = $result;
            }
        } finally {
            $this->running = false;
        }
        return $result;
    }

    public function registerAs(string ...$id): Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }
}
