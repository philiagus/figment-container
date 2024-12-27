<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Helper\HelperProvider;
use Philiagus\Figment\Container\Enum\SingletonMode;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;

class ConstructorBuilder
    extends OverwriteConstructorParameterBase
    implements Contract\Builder\ConstructorBuilder, \IteratorAggregate
{


    /** @var array<string, object> */
    private array $singleton = [];
    private bool $singletonDisabled = false;

    private array $running = [];
    private SingletonMode $singletonMode;

    /**
     * @param Contract\Configuration $configuration
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

    public function registerAs(string ...$id): Contract\Builder\Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    public function build(string $id): object
    {
        $helper = $this->helperProvider->get($this->className);
        $singletonMode = $this->singletonMode ?? $helper->getSingletonMode();
        $singleton = $singletonMode->resolve($id);
        if ($singleton !== null && isset($this->singleton[$singleton]))
            return $this->singleton[$singleton];

        if ($this->running[$id] ?? false) {
            throw new ContainerRecursionException($id);
        }

        $this->running[$id] = true;
        try {
            $instance = $helper->buildConstructed($this, $id);
            if ($singleton !== null)
                $this->singleton[$singleton] = $instance;
            return $instance;
        } catch (Contract\ContainerTraceException $e) {
            $e->prependContainerTrace($id);
        } finally {
            $this->running[$id] = false;
        }
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
