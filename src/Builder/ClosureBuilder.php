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

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;

class ClosureBuilder implements Contract\Builder\ClosureBuilder, \IteratorAggregate
{
    private object $singleton;
    private array $running = [];

    private bool $useSingleton = true;

    /**
     * @param Contract\Configuration $configuration
     * @param \Closure(Container $container, string $name): object $generator
     */
    public function __construct(
        private readonly Contract\Configuration $configuration,
        private readonly \Closure               $generator
    )
    {
    }

    public function build(string $name): object
    {
        if (isset($this->singleton)) {
            return $this->singleton;
        }
        if ($this->running[$name] ?? false) {
            throw new ContainerRecursionException($name);
        }
        $this->running[$name] = true;
        try {
            $container = new \Philiagus\Figment\Container\Container($this->configuration);
            $result = ($this->generator)($container, $name);
            if (!is_object($result)) {
                throw new ContainerException("Generator did not result in an object");
            }
            if ($this->useSingleton) {
                $this->singleton = $result;
            }
            return $result;
        } catch (ContainerRecursionException $e) {
            $e->prepend($name);
        } finally {
            $this->running[$name] = false;
        }
    }

    public function registerAs(string ...$id): Contract\Builder\Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    public function getIterator(): \Traversable
    {
        yield $this;
    }

    public function disableSingleton(): static
    {
        $this->useSingleton = false;

        return $this;
    }
}
