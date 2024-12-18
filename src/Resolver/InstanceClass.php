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

use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Context\ArrayContext;
use Philiagus\Figment\Container\Context\FallbackContext;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Contract\Provider;
use Philiagus\Figment\Container\Contract\Registrable;
use Philiagus\Figment\Container\Contract\Resolver;
use Philiagus\Figment\Container\ReflectionRegistry;
use ReflectionAttribute;
use Traversable;

class InstanceClass implements Contract\InstanceConfigurator, \IteratorAggregate
{

    private bool $running = false;

    private object $singleton;
    private array $constructorArguments = [];

    private bool $isSingletonDisabled;
    private array $properties;
    private \ReflectionClass $classReflection;

    private ?\ReflectionMethod $constructor;

    /**
     * @param Provider $configuration
     * @param Context $context
     * @param class-string $className
     */
    public function __construct(
        private readonly Contract\Provider $configuration,
        private Contract\Context           $context,
        private readonly string            $className
    )
    {
    }

    public function has(string $id): bool
    {
        return $this->configuration->has($id);
    }

    public function context(): Contract\Context
    {
        return $this->context;
    }

    public function resolve(): object
    {
        if(isset($this->singleton)) {
            return $this->singleton;
        }
        if ($this->running) {
            throw new \LogicException("Recursion detected!");
        }

        $reflection = ReflectionRegistry::getClassReflection($this->className);

        $this->running = true;
        try {
            $instance = $reflection->class->newLazyGhost(
                function (object $object) use ($reflection) {
                    foreach ($reflection->properties as [$property, $attributes])
                        foreach ($attributes as $attribute)
                            $attribute->resolve($this, $property, $object);
                    $reflection->constructor?->invokeArgs($object, $this->constructorArguments);
                }
            );
            if (!$reflection->singletonDisabled) {
                $this->singleton = $instance;
            }
            return $instance;
        } finally {
            $this->running = false;
        }
    }

    public function setContext(Contract\Context|array $context, bool $fallbackToDefault = false): Contract\InstanceConfigurator
    {
        if (!$context instanceof Contract\Context) {
            $context = new ArrayContext($context);
        }

        if ($fallbackToDefault) {
            $this->context = new FallbackContext($context, $this->context);
        } else {
            $this->context = $context;
        }

        return $this;
    }

    public function redirect(string $id, Contract\Resolver|string $resolver): Contract\InstanceConfigurator
    {
        if ($resolver instanceof Contract\Resolver) {
            $this->redirection[$id] = $resolver;
        } else {
            $this->redirection[$id] = $this->configuration->get($id);
        }


        return $this;
    }

    public function get(string $id): Resolver
    {
        return $this->configuration->get($id);
    }

    public function registerAs(string ...$id): Registrable
    {
        $this->configuration->register($this, ...$id);
        return $this;
    }

    public function constructorArguments(...$params): Contract\InstanceConfigurator
    {
        $this->constructorArguments = $params;

        return $this;
    }

    public function getIterator(): Traversable
    {
        yield $this;
    }
}
