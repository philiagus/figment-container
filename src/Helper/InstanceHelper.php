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
use Philiagus\Figment\Container\Container;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Builder\OverwriteConstructorParameterProvider;
use Philiagus\Figment\Container\Exception\ContainerException;
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
     * @throws \ReflectionException
     */
    public function __construct(
        private string $className
    )
    {
        $this->class = new \ReflectionClass($className);
        if (!$this->class->isInstantiable()) {
            throw new ContainerException("Class {$this->className} is not instantiable");
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
    }

    /**
     * @param OverwriteConstructorParameterProvider $builder
     * @param string $forName
     *
     * @return object
     * @throws ReflectionException
     */
    public function buildInjected(OverwriteConstructorParameterProvider $builder, string $forName): object
    {
        if ($this->eagerInstantiation) {
            $params = $this->buildInjectionConstructorParameters($builder, $forName);
            return $this->class->newInstanceArgs($params);
        }

        return $this->class->newLazyGhost(
            fn(object $object) => $this->constructor
                ?->invokeArgs($object, $this->buildInjectionConstructorParameters($builder, $forName))
        );
    }

    /**
     * @param OverwriteConstructorParameterProvider $provider
     * @param string $forName
     *
     * @return array<string, mixed>
     */
    private function buildInjectionConstructorParameters(OverwriteConstructorParameterProvider $provider, string $forName): array
    {
        $container = new Container($provider);
        $arguments = $provider->resolveOverwriteConstructorParameter($forName);
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
                $value = $attribute->resolve($container, $parameter, $hasValue);
                if ($hasValue) {
                    $arguments[$name] = $value;
                    break;
                }
            }

            if (!$hasValue && !$parameter->isDefaultValueAvailable()) {
                throw new ContainerException(
                    "Could not create parameter value for not-defaulted constructor parameter $name of $forName"
                );
            }
        }
        return $arguments;
    }

    /**
     * @param OverwriteConstructorParameterProvider $parameterProvider
     * @param string $forName
     *
     * @return object
     * @throws ContainerException
     */
    public function buildConstructed(OverwriteConstructorParameterProvider $parameterProvider, string $forName): object
    {
        if ($this->eagerInstantiation) {
            try {
                return $this->class->newInstanceArgs(
                    $parameterProvider->resolveOverwriteConstructorParameter($forName)
                );
            } catch (\Throwable $e) {
                throw new ContainerException(
                    "Could not create new instance of class {$this->className}", previous: $e
                );
            }
        }
        return $this->class->newLazyGhost(
            fn(object $object) => $this->constructor?->invokeArgs(
                $object,
                $parameterProvider->resolveOverwriteConstructorParameter($forName)
            )
        );
    }
}
