<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Helper\HelperProvider;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;

class ConstructorBuilder
    extends OverwriteConstructorParameterBase
    implements Contract\Builder\ConstructorBuilder, \IteratorAggregate
{


    private object $singleton;
    private bool $singletonDisabled = false;

    private bool $running = false;

    /**
     * @param Contract\Configuration $configuration
     * @param HelperProvider $reflectionProvider
     * @param class-string $className
     */
    public function __construct(
        Contract\Configuration                          $configuration,
        private readonly Contract\Helper\HelperProvider $reflectionProvider,
        private readonly string                         $className
    )
    {
        parent::__construct($configuration);
    }

    public function disableSingleton(): self
    {
        $this->singletonDisabled = true;

        return $this;
    }

    public function registerAs(string ...$id): Contract\Builder\Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    public function build(string $name): object
    {
        if (isset($this->singleton))
            return $this->singleton;

        if($this->running) {
            throw new ContainerRecursionException($name);
        }

        $reflection = $this->reflectionProvider->get($this->className);
        $this->singletonDisabled = $this->singletonDisabled || $reflection->singletonDisabled;

        $this->running = true;
        try {
            $instance = $reflection->buildConstructed($this, $name);
            if (!$this->singletonDisabled)
                $this->singleton = $instance;
            return $instance;
        } catch (ContainerRecursionException $e) {
            $e->prepend($name);
        } finally {
            $this->running = false;
        }
    }

    public function getIterator(): \Traversable
    {
        yield $this;
    }
}
