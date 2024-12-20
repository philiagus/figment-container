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
use Philiagus\Figment\Container\Exception\ContainerRecursionException;

class InjectionBuilder
    extends OverwriteConstructorParameterBase
    implements Contract\Builder\InjectionBuilder, \IteratorAggregate
{

    private bool $running = false;
    private array $redirection = [];
    private object $singleton;

    /**
     * @param Contract\Configuration $configuration
     * @param class-string $className
     */
    public function __construct(
        Contract\Configuration  $configuration,
        private readonly Contract\Configuration\ReflectionProvider $reflectionProvider,
        private readonly string $className
    )
    {
        parent::__construct($configuration);
    }

    public function build(string $name): object
    {
        if (isset($this->singleton)) {
            return $this->singleton;
        }
        if ($this->running) {
            throw new ContainerRecursionException($name);
        }

        $reflection = $this->reflectionProvider->get($this->className);
        $this->running = true;
        try {
            $instance = $reflection->buildInjected($this, $name);
            if (!$reflection->singletonDisabled)
                $this->singleton = $instance;
            return $instance;
        } catch (ContainerRecursionException $e) {
            $e->prepend($name);
        } finally {
            $this->running = false;
        }
    }

    public function redirect(string $id, Contract\Builder|string $to): static
    {
        $this->redirection[$id] = $to instanceof Contract\Builder
            ? $to
            : $this->configuration->get($id);


        return $this;
    }

    protected function getBuilder(string $id): Contract\Builder
    {
        $target = $this->redirection[$id] ?? $id;
        return $target instanceof Contract\Builder
            ? $target
            : $this->configuration->get($target);
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
}
