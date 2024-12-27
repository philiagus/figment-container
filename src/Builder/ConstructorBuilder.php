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

    private array $running = [];

    /**
     * @param Contract\Configuration $configuration
     * @param HelperProvider $helperProvider
     * @param class-string $className
     */
    public function __construct(
        Contract\Configuration                          $configuration,
        private readonly Contract\Helper\HelperProvider $helperProvider,
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

    public function build(string $id): object
    {
        if (isset($this->singleton))
            return $this->singleton;

        if ($this->running[$id] ?? false) {
            throw new ContainerRecursionException($id);
        }

        $helper = $this->helperProvider->get($this->className);
        $this->singletonDisabled = $this->singletonDisabled || $helper->singletonDisabled;

        $this->running[$id] = true;
        try {
            $instance = $helper->buildConstructed($this, $id);
            if (!$this->singletonDisabled)
                $this->singleton = $instance;
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
}
