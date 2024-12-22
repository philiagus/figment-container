<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Container;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Builder\Registrable;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Philiagus\Figment\Container\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Traversable;

class FactoryBuilder implements Contract\Builder\FactoryBuilder, \IteratorAggregate
{

    private array $runningFor = [];

    public function __construct(
        private readonly Contract\Configuration  $configuration,
        private readonly string|Contract\Factory $factory
    )
    {
    }

    public function build(string $name): object
    {
        $container = new Container($this->configuration);
        if ($this->runningFor[$name] ?? false) {
            throw new ContainerRecursionException($name);
        }
        $this->runningFor[$name] = true;
        try {
            if ($this->factory instanceof Contract\Factory) {
                $factory = $this->factory;
            } else {
                try {
                    $factory = $container->get($this->factory);
                } catch (ContainerExceptionInterface $exception) {
                    throw new ContainerException("Factory {$this->factory} could not be instantiated", previous: $exception);
                }
            }

            if (!$factory instanceof Contract\Factory) {
                throw new ContainerException(
                    "Trying to instantiate $name using factory $this->factory, which is not a Factory instance"
                );
            }

            return $factory->create($container, $name);
        } finally {
            $this->runningFor[$name] = false;
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
