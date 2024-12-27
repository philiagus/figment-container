<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Builder\Registrable;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Psr\Container\ContainerExceptionInterface;
use Traversable;

class FactoryBuilder implements Contract\Builder\FactoryBuilder, \IteratorAggregate
{

    private array $running = [];

    public function __construct(
        private readonly Contract\Configuration  $configuration,
        private readonly string|Contract\Factory $factory
    )
    {
    }

    public function build(string $id): object
    {
        $container = $this->configuration->getContainer();
        if ($this->running[$id] ?? false) {
            throw new ContainerRecursionException($id);
        }
        $this->running[$id] = true;
        try {
            if ($this->factory instanceof Contract\Factory) {
                $factory = $this->factory;
            } else {
                try {
                    $factory = $container->get($this->factory);
                } catch (ContainerExceptionInterface $exception) {
                    throw new ContainerException("Factory $this->factory could not be instantiated", previous: $exception);
                }
            }

            if (!$factory instanceof Contract\Factory) {
                throw new ContainerException(
                    "Trying to instantiate $id using factory $this->factory, which is not a Factory instance"
                );
            }

            return $factory->create($container, $id);
        } catch (Contract\ContainerTraceException $e) {
            $e->prependContainerTrace($id);
        } finally {
            $this->running[$id] = false;
        }
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
}
