<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Builder\Registrable;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
class FactoryBuilder implements Contract\Builder\FactoryBuilder, \IteratorAggregate
{

    private array $running = [];

    /** @var array<string, object> */
    private array $singleton = [];

    private Contract\Factory $factoryInstance;

    public function __construct(
        private readonly Contract\Configuration $configuration,
        private readonly string|Contract\Factory $factory
    )
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function build(string $id): object
    {
        $singleton = null;
        if (isset($this->factoryInstance)) {
            $singleton = $this->factoryInstance
                ->getSingletonMode($id)
                ->resolve($id);
            if ($singleton !== null && isset($this->singleton[$singleton])) {
                return $this->singleton[$singleton];
            }
        }

        if ($this->running[$id] ?? false) {
            throw new ContainerRecursionException($id);
        }
        $container = $this->configuration->getContainer();
        $this->running[$id] = true;
        try {
            if (!isset($this->factoryInstance)) {
                if ($this->factory instanceof Contract\Factory) {
                    $this->factoryInstance = $this->factory;
                } else {
                    try {
                        $factory = $container->get($this->factory);
                    } catch (ContainerExceptionInterface $exception) {
                        throw new ContainerException("Factory $this->factory could not be instantiated", previous: $exception);
                    }
                    if (!$factory instanceof Contract\Factory) {
                        throw new ContainerException(
                            "Trying to instantiate $id using factory $this->factory, which is not a Factory instance"
                        );
                    }
                    $this->factoryInstance = $factory;
                }
                $singleton = $this->factoryInstance
                    ->getSingletonMode($id)
                    ->resolve($id);
            }

            $instance = $this->factoryInstance->create($container, $id);
            if ($singleton !== null) {
                $this->singleton[$singleton] = $instance;
            }
            return $instance;
        } catch (Contract\PrependMessageThrowableInterface $e) {
            $e->prependMessage($id);
        } finally {
            $this->running[$id] = false;
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function registerAs(string ...$id): Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    /** @inheritDoc */
    #[\Override]
    public function getIterator(): \Traversable
    {
        yield $this;
    }
}
