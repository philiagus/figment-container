<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Resolver;

use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Configuration\Registrable;
use Philiagus\Figment\Container\ReflectionRegistry;
use Traversable;

class InstanceConstructed
    extends OverwriteConstructorParameterBase
    implements Configuration\ConstructedConfigurator, \IteratorAggregate
{


    private object $singleton;
    private bool $singletonDisabled = false;

    /**
     * @param Configuration $configuration
     * @param string $className
     */
    public function __construct(
        Configuration           $configuration,
        private readonly string $className
    )
    {
        parent::__construct($configuration);
    }

    public function disableSingleton(): self
    {
        $this->singletonDisabled = true;

        return $this;
    }

    public function registerAs(string ...$id): Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    public function resolve(): object
    {
        if ($this->singleton)
            return $this;

        $reflection = ReflectionRegistry::getClassReflection($this->className);
        $instance = $reflection->ghostConstructed($this);
        if (!$this->singletonDisabled)
            $this->singleton = $instance;
        return $instance;
    }

    public function getIterator(): Traversable
    {
        yield $this;
    }
}
