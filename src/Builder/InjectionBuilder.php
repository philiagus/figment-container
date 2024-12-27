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
use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Helper\HelperProvider;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;

class InjectionBuilder
    extends OverwriteConstructorParameterBase
    implements Contract\Builder\InjectionBuilder, \IteratorAggregate
{

    /** @var array<string, bool> */
    private array $running = [];

    /** @var array<string, Proxy\RedirectionProxy> */
    private array $redirection = [];

    /** @var array<string, object> */
    private array $singleton = [];

    /**
     * @param Configuration $configuration
     * @param HelperProvider $helperProvider
     * @param class-string $className
     */
    public function __construct(
        Contract\Configuration $configuration,
        private readonly Contract\Helper\HelperProvider $helperProvider,
        private readonly string $className
    )
    {
        parent::__construct($configuration);
    }

    /** @inheritDoc */
    public function build(string $id): object
    {
        $helper = $this->helperProvider->get($this->className);
        $singletonMode = $this->singletonMode ?? $helper->getSingletonMode();
        $singleton = $singletonMode->resolve($id);
        if ($singleton !== null && isset($this->singleton[$singleton])) {
            return $this->singleton[$singleton];
        }
        if ($this->running[$id] ?? false) {
            throw new ContainerRecursionException($id);
        }

        $this->running[$id] = true;
        try {
            $instance = $helper->buildInjected($this, $id);
            if ($singleton !== null)
                $this->singleton[$singleton] = $instance;
            return $instance;
        } catch (Contract\ContainerTraceException $e) {
            $e->prependContainerTrace($id);
        } finally {
            $this->running[$id] = false;
        }
    }

    /** @inheritDoc */
    public function get(string $id): Contract\Builder
    {
        return $this->redirection[$id] ?? parent::get($id);
    }

    /** @inheritDoc */
    public function redirect(string $id, Contract\Builder|string $to): static
    {
        $this->redirection[$id] = new Proxy\RedirectionProxy($this->configuration, $to);

        return $this;
    }

    /** @inheritDoc */
    public function has(string $id): bool
    {
        $redirection = $this->redirection[$id] ?? null;
        if ($redirection) {
            return $redirection->exists();
        }
        return $this->configuration->has($id);
    }

    /** @inheritDoc */
    public function registerAs(string ...$id): Contract\Builder\Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    /** @inheritDoc */
    public function getIterator(): \Traversable
    {
        yield $this;
    }
}
