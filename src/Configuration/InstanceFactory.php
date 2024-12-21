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

namespace Philiagus\Figment\Container\Configuration;

use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Attribute\EagerInstantiation;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Builder\OverwriteConstructorParameterProvider;
use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Exception\ContainerException;
use ReflectionException;

/**
 * @internal
 */
readonly class InstanceFactory implements Contract\Configuration\InstanceFactory
{

    public bool $singletonDisabled;

    private bool $eagerInstantiation;

    private \ReflectionClass $class;
    private ?\ReflectionMethod $constructor;

    /** @var array<string, array{\ReflectionParameter, Contract\InjectionAttribute[]}> */
    private array $constructorParameters;

    /**
     * @param class-string $className
     * @throws \ReflectionException
     */
    public function __construct(
        private string $className
    )
    {
        $this->class = new \ReflectionClass($className);
        $this->constructor = $this->class->getConstructor();
        $this->singletonDisabled = !empty($this->class->getAttributes(DisableSingleton::class));
        $this->eagerInstantiation = !empty($this->class->getAttributes(EagerInstantiation::class));
        $constructorParameters = [];
        if ($this->constructor) foreach ($this->constructor->getParameters() as $parameter) {
            $attributes = $parameter->getAttributes(
                Contract\InjectionAttribute::class,
                \ReflectionAttribute::IS_INSTANCEOF
            );
            if ($attributes) {
                $constructorParameters[$parameter->getName()] = [
                    $parameter,
                    array_map(
                        static fn(\ReflectionAttribute $o) => $o->newInstance(),
                        $attributes
                    )
                ];
            }
        }
        $this->constructorParameters = $constructorParameters;
    }

    /**
     * @param Container&OverwriteConstructorParameterProvider $provider
     * @param string $forName
     * @return object
     * @throws ReflectionException
     */
    public function buildInjected(Contract\Container&OverwriteConstructorParameterProvider $provider, string $forName): object
    {
        if ($this->eagerInstantiation) {
            $params = $this->buildInjectionConstructorParameters($provider, $forName);
            return $this->class->newInstanceArgs($params);
        }

        return $this->class->newLazyGhost(
            fn(object $object) => $this->constructor
                ?->invokeArgs($object, $this->buildInjectionConstructorParameters($provider, $forName))
        );
    }

    /**
     * @param OverwriteConstructorParameterProvider&Container $provider
     * @param string $forName
     * @return array<string, mixed>
     */
    private function buildInjectionConstructorParameters(Contract\Container&OverwriteConstructorParameterProvider $provider, string $forName): array
    {
        $arguments = $provider->resolveOverwriteConstructorParameter($forName);
        foreach ($this->constructorParameters as $name => [$parameter, $attributes]) {
            if (array_key_exists($name, $arguments)) {
                continue;
            }
            /** @var Contract\InjectionAttribute $attribute */
            foreach ($attributes as $attribute) {
                $hasValue = false;
                $value = $attribute->resolve($provider, $parameter, $hasValue);
                if ($hasValue) {
                    $arguments[$name] = $value;
                    break;
                }
            }
        }
        return $arguments;
    }

    /**
     * @param OverwriteConstructorParameterProvider $parameterProvider
     * @param string $forName
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
