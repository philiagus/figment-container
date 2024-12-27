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

namespace Philiagus\Figment\Container\Helper;

use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Attribute\EagerInstantiation;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Builder\OverwriteConstructorParameterProvider;
use Philiagus\Figment\Container\Contract\ContainerTraceException;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Philiagus\Figment\Container\Exception\NotFoundException;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

/**
 * @internal
 */
readonly class InstanceHelper implements Contract\Helper\InstanceHelper
{

    public bool $singletonDisabled;
    private bool $eagerInstantiation;
    private \ReflectionClass $class;
    private ?\ReflectionMethod $constructor;

    /** @var array<string, array{\ReflectionParameter, Contract\InjectionAttribute[]}> */
    private array $constructorParameters;

    /**
     * @param class-string $className
     *
     * @throws ContainerException
     */
    public function __construct(
        private string $className
    )
    {
        try {
            $this->class = new \ReflectionClass($className);
            if (!$this->class->isInstantiable()) {
                throw new ContainerException("Class $this->className is not instantiable");
            }
            $this->constructor = $this->class->getConstructor();
            $this->singletonDisabled = !empty($this->class->getAttributes(DisableSingleton::class));
            $this->eagerInstantiation = $this->class->isInternal() || !empty($this->class->getAttributes(EagerInstantiation::class));
            $constructorParameters = [];
            if ($this->constructor) foreach ($this->constructor->getParameters() as $parameter) {
                $constructorParameters[$parameter->getName()] = [
                    $parameter,
                    array_map(
                        static fn(\ReflectionAttribute $o) => $o->newInstance(),
                        $parameter->getAttributes(
                            Contract\InjectionAttribute::class,
                            \ReflectionAttribute::IS_INSTANCEOF
                        )
                    )
                ];
            }
            $this->constructorParameters = $constructorParameters;
        } catch (\Throwable $e) {
            throw new ContainerException(
                "Exception while trying to inspect class $this->className",
                previous: $e
            );
        }
    }

    /**
     * @param OverwriteConstructorParameterProvider $builder
     * @param string $id
     *
     * @return object
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ContainerTraceException
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function buildInjected(OverwriteConstructorParameterProvider $builder, string $id): object
    {
        if ($this->eagerInstantiation) {
            $params = $this->buildInjectionConstructorParameters($builder, $id);
            try {
                return $this->class->newInstanceArgs($params);
            } catch (\Throwable $e) {
                throw new ContainerException(
                    "New instance of class $this->className " .
                    "for id $id could not be created",
                    previous: $e
                );
            }
        }

        return $this->class->newLazyGhost(
            fn(object $object) => $this->constructor
                ?->invokeArgs($object, $this->buildInjectionConstructorParameters($builder, $id))
        );
    }

    /**
     * @param OverwriteConstructorParameterProvider $provider
     * @param string $forId
     *
     * @return array<string, mixed>
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ContainerTraceException
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    private function buildInjectionConstructorParameters(OverwriteConstructorParameterProvider $provider, string $forId): array
    {
        $container = $provider->getContainer();
        $arguments = $provider->resolveOverwriteConstructorParameter($forId);
        /**
         * @var string $name
         * @var \ReflectionParameter $parameter
         * @var Contract\InjectionAttribute[] $attributes
         */
        foreach ($this->constructorParameters as $name => [$parameter, $attributes]) {
            if (array_key_exists($name, $arguments)) {
                continue;
            }
            $hasValue = false;
            foreach ($attributes as $attribute) {
                $value = $attribute->resolve($container, $parameter, $forId, $hasValue);
                if ($hasValue) {
                    $arguments[$name] = $value;
                    break;
                }
            }

            if (!$hasValue && !$parameter->isOptional()) {
                throw new ContainerException(
                    "Could not create parameter value for not-optional constructor parameter $name of $forId"
                );
            }
        }
        return $arguments;
    }

    /**
     * @param OverwriteConstructorParameterProvider $parameterProvider
     * @param string $id
     *
     * @return object
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ContainerTraceException
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     */
    public function buildConstructed(OverwriteConstructorParameterProvider $parameterProvider, string $id): object
    {
        if ($this->eagerInstantiation) {
            try {
                return $this->class->newInstanceArgs(
                    $parameterProvider->resolveOverwriteConstructorParameter($id)
                );
            } catch (\Throwable $e) {
                throw new ContainerException(
                    "Could not create new instance of class $this->className", previous: $e
                );
            }
        }
        return $this->class->newLazyGhost(
            fn(object $object) => $this->constructor?->invokeArgs(
                $object,
                $parameterProvider->resolveOverwriteConstructorParameter($id)
            )
        );
    }
}
