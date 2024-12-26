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
use Philiagus\Figment\Container\Contract\Builder\FactoryBuilder;
use Philiagus\Figment\Container\Contract\Factory;
use Philiagus\Figment\Container\Exception\ContainerException;
use Psr\Container\NotFoundExceptionInterface;

class Configuration implements Contract\Configuration
{

    private array $registry = [];
    private array $lazies = [];
    private Contract\Helper\HelperProvider $helperProvider;

    public function __construct(
        private readonly Contract\Context $context = new EmptyContext()
    )
    {
        $this->helperProvider = new Helper\HelperProvider();

        $this->object(new Container($this))
            ->registerAs('container');
    }

    public function closure(\Closure $closure): Contract\Builder\ClosureBuilder
    {
        return new Builder\ClosureBuilder($this, $closure);
    }

    /**
     * @inheritDoc
     */
    public function injected(string $className): Contract\Builder\InjectionBuilder
    {
        return new Builder\InjectionBuilder($this, $this->helperProvider, $className);
    }

    public function context(): Contract\Context
    {
        return $this->context;
    }

    /**
     * @return Contract\Container
     * @throws NotFoundExceptionInterface
     */
    public function getContainer(): Contract\Container
    {
        /** @var Contract\Container $container */
        $container = $this->get('container')->build('container');
        return $container;
    }

    public function get(string $id): Contract\Builder
    {
        return $this->registry[$id] ?? $this->lazies[$id] ??= new Builder\LazyBuilder($this, $id);
    }

    public function object(object $object): Contract\Builder\ObjectBuilder
    {
        return new Builder\ObjectBuilder($this, $object);
    }

    public function register(Contract\Builder $builder, string ...$id): self
    {
        foreach ($id as $singleId) {
            if (isset($this->registry[$singleId]) && $this->registry[$singleId] !== $builder) {
                throw new ContainerException("Trying to register two services with the same id '$singleId'");
            }
            $this->registry[$singleId] = $builder;
        }

        return $this;
    }

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
    public function constructed(string $className): Contract\Builder\ConstructorBuilder
    {
        return new Builder\ConstructorBuilder($this, $this->helperProvider, $className);
    }

    /** @inheritDoc */
    public function has(string $id): bool
    {
        return isset($this->registry[$id]);
    }

    /** @inheritDoc */
    public function factory(Factory|string $factory): Contract\Builder\FactoryBuilder
    {
        return new Builder\FactoryBuilder($this, $factory);
    }
}
