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
use Philiagus\Figment\Container\Enum\SingletonMode;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;

class ClosureBuilder implements Contract\Builder\ClosureBuilder, \IteratorAggregate
{
    /** @var array<string, object> */
    private array|object $singleton;
    private array $running = [];
    private SingletonMode $singletonMode = SingletonMode::BY_BUILDER;

    /**
     * @param Contract\Configuration $configuration
     * @param \Closure(Container $container, string $id): object $generator
     */
    public function __construct(
        private readonly Contract\Configuration $configuration,
        private readonly \Closure $generator
    )
    {
    }

    public function build(string $id): object
    {
        $singleton = $this->singletonMode->resolve($id);
        if ($singleton !== null && isset($this->singleton[$singleton])) {
            return $this->singleton[$singleton];
        }
        if ($this->running[$id] ?? false) {
            throw new ContainerRecursionException($id);
        }
        $this->running[$id] = true;
        try {
            $container = new \Philiagus\Figment\Container\Container($this->configuration);
            $result = ($this->generator)($container, $id);
            if (!is_object($result)) {
                throw new ContainerException("Generator did not result in an object");
            }
            if ($singleton !== null) {
                $this->singleton[$singleton] = $result;
            }
            return $result;
        } catch (Contract\ContainerTraceException $e) {
            $e->prependContainerTrace($id);
        } finally {
            $this->running[$id] = false;
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

    public function singletonMode(SingletonMode $mode): static
    {
        $this->singletonMode = $mode;

        return $this;
    }
}
