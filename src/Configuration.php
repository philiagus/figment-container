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

use Philiagus\Figment\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Configuration implements Contract\Configuration
{

    private array $registered = [];

    private array $lazies = [];

    private readonly Contract\Context $context;

    public function __construct(
        Contract\Context|array $context = []
    )
    {
        if ($context instanceof Contract\Context) {
            $this->context = $context;
        } else {
            $this->context = new Context\MapContext($context);
        }

        $this->generator(fn() => new Container($this))
            ->registerAs('container');
    }

    public function generator(\Closure $closure): Contract\Builder\GeneratorBuilder
    {
        return new Builder\GeneratorBuilder($this, $closure);
    }

    /**
     * @inheritDoc
     */
    public function injected(string $className): Contract\Builder\InjectionBuilder
    {
        return new Builder\InjectionBuilder($this, $className);
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
        return $this->registered[$id] ?? $this->lazies[$id] ??= new Builder\LazyBuilder($this, $id);
    }

    public function object(object $object): Contract\Builder\ObjectBuilder
    {
        return new Builder\ObjectBuilder($this, $object);
    }

    public function register(Contract\Builder $builder, string ...$id): self
    {
        foreach ($id as $singleId) {
            if (isset($this->registered[$singleId]) && $this->registered[$singleId] !== $builder) {
                throw new ContainerException("Trying to register two services with the same id '$singleId'");
            }
            $this->registered[$singleId] = $builder;
        }

        return $this;
    }

    public function list(?string $id = null): Contract\Builder\ListBuilder
    {
        if ($id === null)
            return new Builder\ListBuilder($this);

        $registeredList = $this->registered[$id] ?? null;
        if ($registeredList === null) {
            return $this->registered[$id] = new Builder\ListBuilder($this);
        }
        if ($registeredList instanceof Contract\Builder\ListBuilder) {
            return $registeredList;
        }

        throw new ContainerException(
            "Trying to access '$id' as list, which is already registered as not being a list"
        );
    }

    public function constructed(string $className): Contract\Builder\ConstructorBuilder
    {
        return new Builder\ConstructorBuilder($this, $className);
    }

    public function has(string $id): bool
    {
        return isset($this->registered[$id]);
    }
}
