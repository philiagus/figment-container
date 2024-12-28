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

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Context\EmptyContext;
use Philiagus\Figment\Container\Contract\Factory;
use Philiagus\Figment\Container\Exception\ContainerConfigurationException;
use Philiagus\Figment\Container\Exception\ContainerException;

/**
 * Entry configuration used by the framework
 *
 * First create an instance of this configuration object and use its exposed
 * methods to configure the services you want the container to provide.
 */
final class Configuration implements Contract\Configuration
{

    private array $registry = [];
    private array $lazies = [];
    private readonly Contract\Helper\HelperProvider $helperProvider;

    private readonly Contract\Container $container;

    /**
     *
     *
     * @param Contract\Context $context
     */
    public function __construct(
        private readonly Contract\Context $context = new EmptyContext()
    )
    {
        $this->helperProvider = new Helper\HelperProvider();

        $this->container = new Container($this);
        $this->object($this->container)
            ->registerAs('container');
    }

    /** @inheritDoc */
    #[\Override]
    public function object(object $object): Contract\Builder\ObjectBuilder
    {
        return new Builder\ObjectBuilder($this, $object);
    }

    /** @inheritDoc */
    #[\Override]
    public function closure(\Closure $closure): Contract\Builder\ClosureBuilder
    {
        return new Builder\ClosureBuilder($this, $closure);
    }

    /** @inheritDoc */
    #[\Override]
    public function attributed(string $className): Contract\Builder\AttributedBuilder
    {
        return new Builder\AttributedBuilder($this, $this->helperProvider, $className);
    }

    /** @inheritDoc */
    #[\Override]
    public function context(): Contract\Context
    {
        return $this->context;
    }

    /** @inheritDoc */
    #[\Override]
    public function getContainer(): Contract\Container
    {
        return $this->container;
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $id): Contract\Builder
    {
        return $this->registry[$id] ?? $this->lazies[$id] ??= new Builder\LazyBuilder($this, $id);
    }

    /** @inheritDoc */
    #[\Override]
    public function register(Contract\Builder $builder, string ...$id): self
    {
        foreach ($id as $singleId) {
            if (
                isset($this->registry[$singleId]) &&
                $this->registry[$singleId] !== $builder
            ) {
                throw new ContainerConfigurationException(
                    "Trying to register two services with " .
                    "the same id '$singleId'"
                );
            }
            $this->registry[$singleId] = $builder;
        }

        return $this;
    }

    /** @inheritDoc */
    #[\Override]
    public function list(?string $id = null): Contract\Builder\ListBuilder
    {
        if ($id === null)
            return new Builder\ListBuilder($this);

        $registeredList = $this->registry[$id] ?? null;
        if ($registeredList === null) {
            return $this->registry[$id] = new Builder\ListBuilder($this);
        }
        if ($registeredList instanceof Contract\Builder\ListBuilder) {
            return $registeredList;
        }

        throw new ContainerException(
            "Trying to access '$id' as list, which is already registered as not being a list"
        );
    }

    /** @inheritDoc */
    #[\Override]
    public function constructed(string $className): Contract\Builder\ConstructorBuilder
    {
        return new Builder\ConstructorBuilder($this, $this->helperProvider, $className);
    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $id): bool
    {
        return isset($this->registry[$id]);
    }

    /** @inheritDoc */
    #[\Override]
    public function factory(Factory|string $factory): Contract\Builder\FactoryBuilder
    {
        return new Builder\FactoryBuilder($this, $factory);
    }
}
