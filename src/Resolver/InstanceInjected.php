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

use Override;
use Philiagus\Figment\Container\Context\ArrayContext;
use Philiagus\Figment\Container\Context\FallbackContext;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Configuration\Registrable;
use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Contract\Resolver;
use Philiagus\Figment\Container\ReflectionRegistry;
use Traversable;

class InstanceInjected
    extends OverwriteConstructorParameterBase
    implements Contract\Configuration\InjectionConfigurator, \IteratorAggregate
{

    private bool $running = false;
    private array $redirection = [];

    private object $singleton;

    /**
     * @param Configuration $configuration
     * @param class-string $className
     */
    public function __construct(
        Contract\Configuration   $configuration,
        private readonly string  $className
    )
    {
        parent::__construct($configuration);
    }

    public function resolve(): object
    {
        if (isset($this->singleton)) {
            return $this->singleton;
        }
        if ($this->running) {
            throw new \LogicException("Recursion detected!");
        }

        $reflection = ReflectionRegistry::getClassReflection($this->className);

        $this->running = true;
        try {
            $instance = $reflection->ghostInjected($this);
            if (!$reflection->singletonDisabled)
                $this->singleton = $instance;
            return $instance;
        } finally {
            $this->running = false;
        }
    }

    public function redirect(string $id, Contract\Resolver|string $resolver): static
    {
        if ($resolver instanceof Contract\Resolver) {
            $this->redirection[$id] = $resolver;
        } else {
            $this->redirection[$id] = $this->configuration->get($id);
        }


        return $this;
    }

    public function registerAs(string ...$id): Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    public function getIterator(): Traversable
    {
        yield $this;
    }

    public function get(string $id): Resolver
    {
        $target = $this->redirection[$id] ?? $id;
        return $target instanceof Resolver
            ? $target
            : $this->configuration->get($target);
    }

    public function has(string $id): bool
    {
        $target = $this->redirection[$id] ?? $id;
        return $target instanceof Resolver || $this->configuration->has($target);
    }
}
